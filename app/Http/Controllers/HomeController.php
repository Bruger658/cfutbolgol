<?php

namespace App\Http\Controllers;

use App\Models\GalleryItems;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('index', [
            // 'galleryItems' => GalleryItem::query()
            //     ->where('is_active', true)
            'galleryItems' => GalleryItems::query()
                ->latest()
                ->get(),
        ]);
    }
}