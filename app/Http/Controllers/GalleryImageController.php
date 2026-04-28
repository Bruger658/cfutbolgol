<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryImageController extends Controller
{
    public function index(): View
    {
        $images = GalleryImage::query()
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('gallery-images.index', compact('images'));
    }

    public function create(): View
    {
        return view('gallery-images.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $path = $request->file('image')->store('gallery', 'public');

        GalleryImage::create([
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'image_path' => $path,
        ]);

        return redirect()
            ->route('gallery-images.index')
            ->with('status', 'Imagen agregada correctamente a la galería.');
    }

    public function edit(GalleryImage $galleryImage): View
    {
        return view('gallery-images.edit', compact('galleryImage'));
    }

    public function update(Request $request, GalleryImage $galleryImage): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($galleryImage->image_path && Storage::disk('public')->exists($galleryImage->image_path)) {
                Storage::disk('public')->delete($galleryImage->image_path);
            }

            $galleryImage->image_path = $request->file('image')->store('gallery', 'public');
        }

        $galleryImage->fill([
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        $galleryImage->save();

        return redirect()
            ->route('gallery-images.index')
            ->with('status', 'Imagen actualizada correctamente.');
    }

    public function destroy(GalleryImage $galleryImage): RedirectResponse
    {
        if ($galleryImage->image_path && Storage::disk('public')->exists($galleryImage->image_path)) {
            Storage::disk('public')->delete($galleryImage->image_path);
        }

        $galleryImage->delete();

        return redirect()
            ->route('gallery-images.index')
            ->with('status', 'Imagen eliminada correctamente.');
    }
}