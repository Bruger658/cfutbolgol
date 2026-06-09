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

                if (! $product || $product->stock < 1) {
                    return null;
                }

                $quantity = min($quantity, $product->stock);

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => (float) $product->price * $quantity,
                ];
            })
            ->filter()
            ->values();

        $normalizedCart = $items->mapWithKeys(fn (array $item): array => [
            $item['product']->id => $item['quantity'],
        ])->all();

        if ($normalizedCart !== $cart) {
            $request->session()->put('cart', $normalizedCart);
        }    

        $total = $items->sum('subtotal');
        $itemCount = $items->sum('quantity');

        return view('cart.show', compact('items', 'total', 'itemCount'));
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


    public function storeMany(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'selected_products' => ['required', 'array', 'min:1'],
            'selected_products.*' => ['integer', 'distinct', 'exists:products,id'],
            'quantities' => ['nullable', 'array'],
            'quantities.*' => ['integer', 'min:1'],
        ], [
            'selected_products.required' => 'Tildá al menos un producto para agregarlo al carrito.',
            'selected_products.min' => 'Tildá al menos un producto para agregarlo al carrito.',
        ]);

        $productIds = collect($validated['selected_products'])->map(fn ($id): int => (int) $id);
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');
        $quantities = $validated['quantities'] ?? [];
        $cart = $this->cart($request);

        foreach ($productIds as $productId) {
            $product = $products->get($productId);
            $quantity = (int) ($quantities[$productId] ?? 1);

            if (! $product || $product->stock < 1) {
                return back()->withErrors([
                    'cart' => $product
                        ? "{$product->name} ya no tiene stock disponible."
                        : 'Uno de los productos seleccionados ya no está disponible.',
                ])->withInput();
            }

            if ($quantity > $product->stock) {
                return back()->withErrors([
                    'cart' => "La cantidad elegida de {$product->name} supera el stock disponible.",
                ])->withInput();
            }

            $cart[$productId] = min(($cart[$productId] ?? 0) + $quantity, $product->stock);
        }

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.show')->with(
            'status',
            $productIds->count() === 1
                ? 'Producto agregado al carrito.'
                : $productIds->count().' productos agregados al carrito.',
        );
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

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget(['cart', 'pending_cart_checkout']);

        return redirect()->route('tienda')->with('status', 'Cancelaste la compra y vaciaste el carrito.');
    }


    private function cart(Request $request): array
    {
        return collect($request->session()->get('cart', []))
            ->map(fn ($quantity) => (int) $quantity)
            ->filter(fn (int $quantity) => $quantity > 0)
            ->all();
    }
}