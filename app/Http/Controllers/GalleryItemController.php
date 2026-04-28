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
        return view('gallery-items.index', [
            'galleryItems' => GalleryItems::query()->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('gallery-items.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        $path = $request->file('image')->store('gallery-items', 'public');

        GalleryItems::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'image_url' => $path,
        ]);

        return redirect()->route('gallery-items.index')->with('success', 'Imagen creada correctamente.');
    }

    public function edit(GalleryItems $galleryItem): View
    {
        return view('gallery-items.edit', [
            'galleryItem' => $galleryItem,
        ]);
    }

    public function update(Request $request, GalleryItems $galleryItem): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'description' => ['nullable', 'string'],
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

    public function destroy(GalleryItems $galleryItem): RedirectResponse
    {
        $galleryItem->delete();

        return to_route('gallery-items.index')->with('status', 'Elemento eliminado correctamente.');
    }
}