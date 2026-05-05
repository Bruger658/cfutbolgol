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
        return view('gallery-items.index', compact('items'));
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
            'image' => ['required', 'image', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

      
        $data['is_active'] = $request->boolean('is_active');
        $data['image_path'] = $request->file('image')->store('gallery', 'public');

        
        GalleryItems::create([
            'title' => $data['title'],
            'image_path' => $data['image_path'],
            'is_active' => $data['is_active'],
        ]);

        return redirect()->route('gallery-items.index')->with('success', 'Imagen creada correctamente.');
    }


    public function edit(GalleryItems $galleryItem): View
    {
       return view('gallery-items.index', compact('galleryItem'));
    }

    public function update(Request $request, GalleryItems $galleryItem): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

       
         if ($request->hasFile('image')) {
            $galleryItem->image_path = $request->file('image')->store('gallery', 'public');
        }

        $galleryItem->title = $data['title'];
        $galleryItem->is_active = $request->boolean('is_active');
        $galleryItem->save();

        return redirect()->route('gallery-items.index');        
    }

    public function destroy(GalleryItems $galleryItem): RedirectResponse
    {
        $galleryItem->delete();

        return redirect()->route('gallery-items.index');
    }

}