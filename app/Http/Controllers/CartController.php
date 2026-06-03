<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(Request $request): View
    {
        $cart = $this->cart($request);
        $products = Product::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = collect($cart)
            ->map(function (int $quantity, int|string $productId) use ($products) {
                $product = $products->get((int) $productId);

                if (! $product) {
                    return null;
                }

                $quantity = min($quantity, max($product->stock, 1));

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => (float) $product->price * $quantity,
                ];
            })
            ->filter()
            ->values();

        $total = $items->sum('subtotal');

        return view('cart.show', compact('items', 'total'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($product->stock < 1) {
            return back()->withErrors(['cart' => 'El producto seleccionado no tiene stock disponible.']);
        }

        $cart = $this->cart($request);
        $quantity = (int) ($validated['quantity'] ?? 1);
        $cart[$product->id] = min(($cart[$product->id] ?? 0) + $quantity, $product->stock);

        $request->session()->put('cart', $cart);

        return back()->with('status', 'Producto agregado al carrito.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $cart = $this->cart($request);

        if ($validated['quantity'] === 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = min($validated['quantity'], max($product->stock, 0));
        }

        $request->session()->put('cart', $cart);

        return back()->with('status', 'Carrito actualizado.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $cart = $this->cart($request);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);

        return back()->with('status', 'Producto quitado del carrito.');
    }

    private function cart(Request $request): array
    {
        return collect($request->session()->get('cart', []))
            ->map(fn ($quantity) => (int) $quantity)
            ->filter(fn (int $quantity) => $quantity > 0)
            ->all();
    }
}