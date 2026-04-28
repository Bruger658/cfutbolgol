<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicationController extends Controller
{
    public function create(): View
    {
        return view('publications.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['required', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = $request->file('image')->store('publications', 'public');
        $isActive = (bool) ($validated['is_active'] ?? false);

        $publication = Publication::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
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
        return view('publications.edit', compact('publication'));
    }

    public function update(Request $request, Publication $publication): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $publication->image_path = $request->file('image')->store('publications', 'public');
        }

        $isActive = (bool) ($validated['is_active'] ?? false);

        $publication->title = $validated['title'];
        $publication->description = $validated['description'];
        $publication->published_at = $isActive ? ($validated['published_at'] ?? now()->toDateString()) : null;
        $publication->is_active = $isActive;
        $publication->save();

        return back()->with('status', 'Publicación actualizada correctamente.');
    }
}