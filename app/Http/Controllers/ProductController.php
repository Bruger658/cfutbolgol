<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()->latest()->paginate(12);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create', ['product' => new Product()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($this->validateProduct($request));

        return redirect()->route('products.index')->with('status', 'Producto creado correctamente.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validateProduct($request));

        return redirect()->route('products.index')->with('status', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('status', 'Producto eliminado correctamente.');
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);
    }
}