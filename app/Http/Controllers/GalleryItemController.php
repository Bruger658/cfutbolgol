<?php

namespace App\Http\Controllers;

use App\Models\GalleryItems;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryItemController extends Controller
{
    public function index(): View
    {
        $items = GalleryItems::query()->latest()->get();

        return view('gallery_items.index', compact('items'));
    }

    public function create(): View
    {
        return view('gallery_items.create', [
            'galleryItem' => new GalleryItems(['is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image_path' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        GalleryItems::create($data);

        return redirect()->route('gallery-items.index');
    }

    public function edit(GalleryItems $galleryItem): View
    {
        return view('gallery_items.edit', compact('galleryItem'));
    }

    public function update(Request $request, GalleryItems $galleryItem): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image_path' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $galleryItem->update($data);

        return redirect()->route('gallery-items.index');
    }
}