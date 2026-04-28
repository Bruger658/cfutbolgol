<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('index', [
            'galleryItems' => GalleryItem::query()
                ->where('is_active', true)
                ->latest()
                ->get(),
        ]);
    }
}