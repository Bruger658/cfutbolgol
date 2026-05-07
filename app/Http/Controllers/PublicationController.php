<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicationController extends Controller
{ public function index(): View
    {
        $publications = Publication::query()->latest()->paginate(12);

        return view('noticias.noticias', compact('publications'));
    }


    public function create(): View
    {
         return view('noticias.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'in:institucional,edefi,bafi,futsala,futsal_femenino'],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'image' => ['required', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = $request->file('image')->store('publications', 'public');
        $isActive = (bool) ($validated['is_active'] ?? false);

        $publication = Publication::create([
            'category' => $validated['category'],
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'description' => $validated['content'],
            'image_path' => $imagePath,
            'published_at' => $isActive ? ($validated['published_at'] ?? now()->toDateString()) : null,
            'is_active' => $isActive,
        ]);

        return redirect()
            ->route('publications.edit', $publication)
            ->with('status', 'Publicación creada correctamente.');
    }

    public function edit(Publication $publication): View
    {
        return view('noticias.edit', compact('publication'));
    }

    public function update(Request $request, Publication $publication): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'in:institucional,edefi,bafi,futsala,futsal_femenino'],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $publication->image_path = $request->file('image')->store('publications', 'public');
        }

        $isActive = (bool) ($validated['is_active'] ?? false);

        $publication->category = $validated['category'];
        $publication->title = $validated['title'];
        $publication->excerpt = $validated['excerpt'];
        $publication->content = $validated['content'];
        $publication->description = $validated['content'];
        $publication->published_at = $isActive ? ($validated['published_at'] ?? now()->toDateString()) : null;
        $publication->is_active = $isActive;
        $publication->save();

        return back()->with('status', 'Publicación actualizada correctamente.');
    }
}