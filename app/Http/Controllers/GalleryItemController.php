<?php

namespace App\Http\Controllers;

use App\Models\GalleryItems;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;


class GalleryItemController extends Controller
{
    public function index(): View
    {
       return view('gallery-items.index', ['items' => $items]);
    }

    public function create(): View
    {
        return view('gallery-items.create', [
            'galleryItem' => new GalleryItems(['is_active' => true]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // $path = $request->file('image')->store('gallery-items', 'public');

        $data['image_path'] = $request->file('image')->store('gallery', 'public');
        $data['is_active'] = $request->boolean('is_active');
        unset($data['image']);


        GalleryItems::create($data);

        return redirect()->route('gallery-items.index')->with('success', 'Imagen creada correctamente.');
    }


    public function edit(GalleryItems $galleryItem): View
    {
       return view('gallery-items.edit', compact('galleryItem'));
    }

    public function update(Request $request, GalleryItems $galleryItem): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if (!empty($galleryItem->image_url) && Storage::disk('public')->exists($galleryItem->image_url)) {
                Storage::disk('public')->delete($galleryItem->image_url);
            }

            $validated['image_url'] = $request->file('image')->store('gallery-items', 'public');
        }

        unset($validated['image']);

        $galleryItem->update($validated);

        return to_route('gallery-items.index')->with('status', 'Elemento actualizado correctamente.');
    }

    // public function destroy(GalleryItems $galleryItem): RedirectResponse
    // {
    //     $galleryItem->delete();

    //     return to_route('gallery-items.index')->with('status', 'Elemento eliminado correctamente.');
    // }
}