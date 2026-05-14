<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Fixture;
use App\Models\GalleryItems;
use App\Models\Publication;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('index', [
            // 'galleryItems' => GalleryItem::query()
            //     ->where('is_active', true)
            'galleryItems' => GalleryItems::query()
                ->where('is_active', true)
                ->latest()
                ->get(),
            'publications' => Publication::query()
                ->where('is_active', true)
                ->latest('published_at')
                ->take(6)
                ->get(),
            'fixtures' => Fixture::query()
                ->where('is_active', true)
                ->orderBy('fixture_date')
                ->orderBy('match_time')
                ->take(4)
                ->get(),   
            'events' => Event::query()
                ->where('is_completed', false)
                ->orderBy('starts_at')
                ->take(8)
                ->get(),     
        ]);
    }
}