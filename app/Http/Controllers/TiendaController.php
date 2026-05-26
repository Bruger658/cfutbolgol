<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class TiendaController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->where('stock', '>', 0)
            ->latest()
            ->paginate(12);

        return view('tienda', compact('products'));
    }
}