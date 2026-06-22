<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Fixture;
use App\Models\GalleryItems;
use App\Models\Product;
use App\Models\Publication;
use App\Models\Staff;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('index', [
            'galleryItems' => $this->safeCollection(fn () => GalleryItems::query()
                ->where('is_active', true)
                ->latest() 
                ->get()),
            'publications' => $this->safeCollection(fn () => Publication::query()               
                ->where('is_active', true)
                ->latest('published_at')
                ->take(6)
                ->get()),
            'fixtures' => $this->safeCollection(fn () => Fixture::query()
                ->where('is_active', true)
                ->orderBy('fixture_date')
                ->orderBy('match_time')
                ->take(4)
                ->get()),
            'events' => $this->safeCollection(fn () => Event::query()
                ->where('is_completed', false)                
                ->orderBy('starts_at')
                ->take(8)
                ->get()),
            'featuredProducts' => $this->safeCollection(fn () => Product::query()
                ->where('stock', '>', 0)
                ->latest()
                ->take(3)
                ->get()),
            'storeProducts' => $this->safeCollection(fn () => Product::query()
                ->where('stock', '>', 0)
                ->latest()
                ->get()),
            'staffMembers' => $this->safeCollection(fn () => Staff::query()
                ->where('is_active', true)
                ->orderBy('display_order')
                ->orderBy('name')
                ->get()),    
        ]);
    }

    /**
     * Keep the public home page available while a fresh local database is
     * being created or before migrations have been run.
     */
    private function safeCollection(callable $query): Collection
    {
        try {
            return $query();
        } catch (QueryException) {
            return collect();
        }
    }
}
