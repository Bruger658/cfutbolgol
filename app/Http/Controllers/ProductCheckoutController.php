<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class ProductCheckoutController extends Controller
{
    public function prepare(Product $product): View|RedirectResponse
    {
        if ($product->stock < 1) {
            return redirect()->route('tienda')->withErrors([
                'product' => 'El producto seleccionado no tiene stock disponible.',
            ]);
        }

        return view('products.prepare-checkout', compact('product'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'delivery_method' => ['nullable', 'in:shipping,pickup'],
        ]);

        if ($validated['quantity'] > $product->stock) {
            return back()->withErrors(['quantity' => 'La cantidad supera el stock disponible.'])->withInput();
        }

        $accessToken = config('services.mercado_pago.access_token');

        if (blank($accessToken)) {
            return back()->withErrors([
                'mercado_pago' => 'Mercado Pago no está configurado. Agregá MERCADO_PAGO_ACCESS_TOKEN en el archivo .env.',
            ])->withInput();
        }

        $order = ProductOrder::create([
            'product_id' => $product->id,
            'user_id' => $request->user()?->id,
            'quantity' => $validated['quantity'],
            'unit_price' => $product->price,
            'total_price' => (float) $product->price * $validated['quantity'],
            'status' => 'pending',
            'payment_provider' => 'mercado_pago',
            'delivery_method' => ['nullable', 'in:shipping,pickup'],
        ]);

        try {
            $preference = $this->createPreference($order, $product, $accessToken);
        } catch (ConnectionException) {
            $order->update(['status' => 'failed']);

            return back()->withErrors([
                'mercado_pago' => 'No pudimos conectar con Mercado Pago. Intentá nuevamente en unos minutos.',
            ])->withInput();
        }

        if ($preference->failed()) {
            $order->update(['status' => 'failed']);

            return back()->withErrors([
                'mercado_pago' => 'Mercado Pago rechazó la creación del checkout. Revisá las credenciales y volvé a intentar.',
            ])->withInput();
        }

        $preferenceData = $preference->json();
        $checkoutUrl = $preferenceData['init_point']
            ?? $preferenceData['sandbox_init_point']
            ?? null;

        if (blank($checkoutUrl)) {
            $order->update(['status' => 'failed']);

            return back()->withErrors([
                'mercado_pago' => 'Mercado Pago no devolvió una URL de pago válida.',
            ])->withInput();
        }

        $order->update([
            'provider_reference' => $preferenceData['id'] ?? null,
            'checkout_url' => $checkoutUrl,
        ]);

        return redirect()->away($checkoutUrl);
    }

    public function show(ProductOrder $order): View
    {
        return view('products.checkout', compact('order'));
    }

    public function success(Request $request, ProductOrder $order): View
    {
        $this->syncOrderFromReturn($request, $order);

        return view('products.checkout', compact('order'));
    }

    public function failure(Request $request, ProductOrder $order): View
    {
        $this->syncOrderFromReturn($request, $order, 'failed');

        return view('products.checkout', compact('order'));
    }

    public function pending(Request $request, ProductOrder $order): View
    {
        $this->syncOrderFromReturn($request, $order, 'pending');

        return view('products.checkout', compact('order'));
    }

    public function webhook(Request $request)
    {
        $paymentId = $request->input('data.id')
            ?? $request->input('id')
            ?? $request->query('data_id')
            ?? $request->query('id');

        if (blank($paymentId)) {
            return response()->json(['message' => 'No payment id received.'], 202);
        }

        $accessToken = config('services.mercado_pago.access_token');

        if (blank($accessToken)) {
            return response()->json(['message' => 'Mercado Pago is not configured.'], 500);
        }

        try {
            $payment = Http::withToken($accessToken)
                ->acceptJson()
                ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");
        } catch (ConnectionException) {
            return response()->json(['message' => 'Could not connect to Mercado Pago.'], 503);
        }

        if ($payment->failed()) {
            return response()->json(['message' => 'Could not verify payment.'], 422);
        }

        $paymentData = $payment->json();
        $orderId = $paymentData['external_reference'] ?? null;

        if (blank($orderId)) {
            return response()->json(['message' => 'Payment has no external reference.'], 202);
        }

        $order = ProductOrder::find($orderId);

        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $this->applyPaymentStatus($order, $paymentData['status'] ?? null, (string) $paymentId);

        return response()->json(['message' => 'Order updated.']);
    }

    private function createPreference(ProductOrder $order, Product $product, string $accessToken)
    {
        return Http::withToken($accessToken)
            ->acceptJson()
            ->post('https://api.mercadopago.com/checkout/preferences', [
                'items' => [[
                    'id' => (string) $product->id,
                    'title' => $product->name,
                    'description' => $product->description,
                    'picture_url' => $product->image_url,
                    'quantity' => $order->quantity,
                    'currency_id' => config('services.mercado_pago.currency', 'ARS'),
                    'unit_price' => (float) $order->unit_price,
                ]],
                'external_reference' => (string) $order->id,
                'back_urls' => [
                    'success' => route('products.checkout.success', $order),
                    'failure' => route('products.checkout.failure', $order),
                    'pending' => route('products.checkout.pending', $order),
                ],
                'auto_return' => 'approved',
                'notification_url' => route('products.checkout.webhook'),
                'metadata' => [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'delivery_method' => $order->delivery_method,
                ],
            ]);
    }

    private function syncOrderFromReturn(Request $request, ProductOrder $order, ?string $fallbackStatus = null): void
    {
        $status = $request->query('collection_status')
            ?? $request->query('status')
            ?? $fallbackStatus;

        $paymentId = $request->query('payment_id')
            ?? $request->query('collection_id');

        $this->applyPaymentStatus($order, $status, $paymentId);
    }

    private function applyPaymentStatus(ProductOrder $order, ?string $mercadoPagoStatus, ?string $paymentId = null): void
    {
        $status = match ($mercadoPagoStatus) {
            'approved' => 'paid',
            'rejected', 'cancelled', 'refunded', 'charged_back', 'failed' => 'failed',
            'in_process', 'in_mediation', 'pending' => 'pending',
            default => $order->status,
        };

        DB::transaction(function () use ($order, $status, $paymentId): void {
            $order->refresh();

            $updates = ['status' => $status];

            if (filled($paymentId)) {
                $updates['provider_payment_id'] = $paymentId;
            }

            if ($status === 'paid' && $order->status !== 'paid') {
                $updates['paid_at'] = Carbon::now();
                $order->product()->lockForUpdate()->first()?->decrement('stock', $order->quantity);
            }

            $order->update($updates);
        });
    }
}