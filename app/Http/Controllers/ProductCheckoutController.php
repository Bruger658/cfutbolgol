<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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

        $order = ProductOrder::create([
            'product_id' => $product->id,
            'user_id' => $request->user()?->id,
            'quantity' => $validated['quantity'],
            'unit_price' => $product->price,
            'total_price' => (float) $product->price * $validated['quantity'],
            'status' => 'pending',
            'payment_provider' => 'mercado_pago',
            'delivery_method' => $validated['delivery_method'] ?? 'shipping',
        ]);

       return $this->startCheckout($request, collect([$order]), (string) $order->id);
    }

    public function storeCart(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_method' => ['required', 'in:shipping,pickup'],
        ]);       

        $cart = collect($request->session()->get('cart', []))
            ->map(fn ($quantity) => (int) $quantity)
            ->filter(fn (int $quantity) => $quantity > 0);

        if ($cart->isEmpty()) {
            return redirect()->route('cart.show')->withErrors(['cart' => 'Tu carrito está vacío.']);    
            
        }

        $products = Product::query()
            ->whereIn('id', $cart->keys())
            ->get()
            ->keyBy('id');
        foreach ($cart as $productId => $quantity) {
            $product = $products->get((int) $productId);

            if (! $product || $quantity > $product->stock) {
                return redirect()->route('cart.show')->withErrors([
                    'cart' => $product
                        ? "No hay stock suficiente de {$product->name}."
                        : 'Uno de los productos del carrito ya no está disponible.',
                ]);
            }  
        }
        
        $checkoutGroup = (string) Str::uuid();
        $orders = DB::transaction(function () use ($cart, $products, $request, $validated, $checkoutGroup): Collection {
            return $cart->map(function (int $quantity, int|string $productId) use ($products, $request, $validated, $checkoutGroup): ProductOrder {
                $product = $products->get((int) $productId);

                 return ProductOrder::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()?->id,
                    'checkout_group' => $checkoutGroup,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => (float) $product->price * $quantity,
                    'status' => 'pending',
                    'payment_provider' => 'mercado_pago',
                    'delivery_method' => $validated['delivery_method'],
                ]);
            })->values();
        });

        return $this->startCheckout($request, $orders, $checkoutGroup, true);
    }

    public function show(ProductOrder $order): View
    {
        return $this->checkoutView($order);
    }

    public function success(Request $request, ProductOrder $order): View
    {
        $this->syncOrderFromReturn($request, $order);

          if ($order->fresh()->status === 'paid') {
            $request->session()->forget('cart');
        }

        return $this->checkoutView($order);
    }

    public function failure(Request $request, ProductOrder $order): View
    {
        $this->syncOrderFromReturn($request, $order, 'failed');

        return $this->checkoutView($order);
    }

    public function pending(Request $request, ProductOrder $order): View
    {
        $this->syncOrderFromReturn($request, $order, 'pending');

        return $this->checkoutView($order);
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
        $externalReference = $paymentData['external_reference'] ?? null;

        if (blank($externalReference)) {
            return response()->json(['message' => 'Payment has no external reference.'], 202);
        }

       $order = ProductOrder::query()->where('checkout_group', $externalReference)->first();

        if (! $order && ctype_digit((string) $externalReference)) {
            $order = ProductOrder::find((int) $externalReference);
        }

        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $this->applyPaymentStatus($order, $paymentData['status'] ?? null, (string) $paymentId);

        return response()->json(['message' => 'Order updated.']);
    }

     private function startCheckout(Request $request, Collection $orders, string $externalReference, bool $fromCart = false): RedirectResponse
    {
        $accessToken = config('services.mercado_pago.access_token');

        if (blank($accessToken)) {
            $this->deletePendingOrders($orders);

            return back()->withErrors([
                'mercado_pago' => 'Mercado Pago no está configurado. Agregá MERCADO_PAGO_ACCESS_TOKEN en el archivo .env.',
            ])->withInput();
        }

        $orders->each->load('product');
        $representativeOrder = $orders->first();

        try {
            $preference = $this->createPreference($orders, $representativeOrder, $externalReference, $accessToken);
        } catch (ConnectionException) {
            $this->deletePendingOrders($orders);

            return back()->withErrors([
                'mercado_pago' => 'No se pudo conectar con Mercado Pago. Intentá nuevamente en unos minutos.',
            ])->withInput();
        }

        if ($preference->failed()) {
            $this->deletePendingOrders($orders);

            return back()->withErrors([
                'mercado_pago' => 'Mercado Pago rechazó la creación del checkout. Revisá las credenciales y volvé a intentar.',
            ])->withInput();
        }

        $preferenceData = $preference->json();
        $checkoutUrl = $preferenceData['init_point']
            ?? $preferenceData['sandbox_init_point']
            ?? null;

        if (blank($checkoutUrl)) {
            $orders->each(fn (ProductOrder $order) => $order->update(['status' => 'failed']));

            return back()->withErrors([
                'mercado_pago' => 'Mercado Pago no devolvió una URL de pago válida.',
            ])->withInput();
        }

        $orders->each(fn (ProductOrder $order) => $order->update([
            'provider_reference' => $preferenceData['id'] ?? null,
            'checkout_url' => $checkoutUrl,
        ]));

        if ($fromCart) {
            $request->session()->put('pending_cart_checkout', $externalReference);
        }

        return redirect()->away($checkoutUrl);
    }

    private function createPreference(Collection $orders, ProductOrder $representativeOrder, string $externalReference, string $accessToken)
    {
        return Http::withToken($accessToken)
            ->acceptJson()
            ->post('https://api.mercadopago.com/checkout/preferences', [
                'items' => $orders->map(fn (ProductOrder $order): array => [
                    'id' => (string) $order->product_id,
                    'title' => $order->product->name,
                    'description' => $order->product->description,
                    'picture_url' => $order->product->image_url,
                    'quantity' => $order->quantity,
                    'currency_id' => config('services.mercado_pago.currency', 'ARS'),
                    'unit_price' => (float) $order->unit_price,
                 ])->values()->all(),
                'external_reference' => $externalReference,    
                
                'back_urls' => [
                   'success' => route('products.checkout.success', $representativeOrder),
                    'failure' => route('products.checkout.failure', $representativeOrder),
                    'pending' => route('products.checkout.pending', $representativeOrder),
                ],
                'auto_return' => 'approved',
                'notification_url' => route('products.checkout.webhook'),
                'metadata' => [
                    'order_id' => $representativeOrder->id,
                    'checkout_group' => $representativeOrder->checkout_group,
                    'delivery_method' => $representativeOrder->delivery_method,
                ],
            ]);
    }

     private function checkoutView(ProductOrder $order): View
    {
        $orders = $this->relatedOrders($order)->load('product');
        $order = $orders->firstWhere('id', $order->id) ?? $orders->first();
        $total = $orders->sum(fn (ProductOrder $item) => (float) $item->total_price);

        return view('products.checkout', compact('order', 'orders', 'total'));
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
            $orders = $this->relatedOrders($order, true);

             foreach ($orders as $relatedOrder) {
                $updates = ['status' => $status];

            if (filled($paymentId)) {
                    $updates['provider_payment_id'] = $paymentId;
            }

            if ($status === 'paid' && $relatedOrder->status !== 'paid') {
                    $updates['paid_at'] = Carbon::now();
                    $product = Product::query()->lockForUpdate()->find($relatedOrder->product_id);

                    if ($product) {
                        $product->decrement('stock', min($relatedOrder->quantity, $product->stock));
                    }
            }
            $relatedOrder->update($updates);
            }
        });
    }

    private function relatedOrders(ProductOrder $order, bool $lock = false): Collection
    {
        $query = ProductOrder::query()
            ->when(
                filled($order->checkout_group),
                fn ($query) => $query->where('checkout_group', $order->checkout_group),
                fn ($query) => $query->whereKey($order->id),
            )
            ->orderBy('id');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->get();
    }

    private function deletePendingOrders(Collection $orders): void
    {
        ProductOrder::query()->whereKey($orders->pluck('id')->all())->where('status', 'pending')->delete();
    }
}