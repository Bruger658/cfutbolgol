<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TiendaController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Product::query()                
                ->where('stock', '>', 0)
                ->whereNotNull('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category');

                $sizes = Product::query()
                ->where('stock', '>', 0)
                ->whereNotNull('size')
                ->distinct()
                ->orderBy('size')
                ->pluck('size');

                $products = Product::query()
                ->withCount([
                    'orders',
                    'orders as paid_orders_count' => fn ($query) => $query->where('status', 'paid'),
                ])    
                ->where('stock', '>', 0)
                ->when($request->filled('category'), fn ($query) => $query->where('category', $request->input('category')))
                ->when($request->filled('size'), fn ($query) => $query->where('size', $request->input('size')))
                ->latest()
                ->paginate(12)
                ->withQueryString();

            $cartCount = collect($request->session()->get('cart', []))->sum();

                return view('tienda', compact('products', 'categories', 'sizes', 'cartCount'));
        }
    }