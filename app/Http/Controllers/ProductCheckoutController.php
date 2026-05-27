<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductCheckoutController extends Controller
{

    public function prepare(Product $product): View
    {
        return view('products.prepare-checkout', compact('product'));
    }
    
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($validated['quantity'] > $product->stock) {
            return back()->withErrors(['quantity' => 'La cantidad supera el stock disponible.'])->withInput();
        }

        $order = ProductOrder::create([
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'quantity' => $validated['quantity'],
            'unit_price' => $product->price,
            'total_price' => $product->price * $validated['quantity'],
            'status' => 'pending',
            'payment_provider' => 'mercado_libre',
            'provider_reference' => 'ML-' . strtoupper(Str::random(12)),
            'checkout_url' => 'https://www.mercadopago.com.ar/checkout/v1/redirect?pref_id=' . urlencode('ML-' . strtoupper(Str::random(20))),
        ]);

        $product->decrement('stock', $validated['quantity']);

       return redirect()->away($order->checkout_url);
    }

    public function show(ProductOrder $order): View
    {
        return view('products.checkout', compact('order'));
    }
}