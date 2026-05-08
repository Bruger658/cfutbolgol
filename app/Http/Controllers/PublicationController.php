<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublicationController extends Controller
{ 
    public function index(): View
    {
        $publications = Publication::query()->latest()->paginate(12);

        return view('noticias.noticias', compact('publications'));
    }

     public function noticias(): View
    {
        return $this->index();
    }



    public function create(): View
    {
        $venues = ['Sede Morón', 'Sede Castelar', 'Sede Ituzaingó'];

        return view('noticias.create', [
            'venues' => $venues,
            'currentVenue' => old('venue', ''),
            'isCustomVenue' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'in:institucional,edefi,bafi,futsala,futsal_femenino'],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string', 'max:255'],
            'venue' => ['required', 'string', 'max:120'],
            'custom_venue' => ['nullable', 'string', 'max:255', 'required_if:venue,nueva'],
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
            'venue' => $validated['venue'],
            'custom_venue' => $validated['venue'] === 'nueva' ? $validated['custom_venue'] : null,
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
        $venues = ['Sede Morón', 'Sede Castelar', 'Sede Ituzaingó'];
        $currentVenue = old('venue', $publication->venue ?? '');

        return view('noticias.edit', [
            'publication' => $publication,
            'venues' => $venues,
            'currentVenue' => $currentVenue,
            'isCustomVenue' => $currentVenue !== '' && ! in_array($currentVenue, $venues, true),
        ]);
    }

    public function update(Request $request, Publication $publication): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'in:institucional,edefi,bafi,futsala,futsal_femenino'],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string', 'max:255'],
            'venue' => ['required', 'in:almafuerte,stylo,nueva'],
            'custom_venue' => ['nullable', 'string', 'max:255', 'required_if:venue,nueva'],
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
         $publication->venue = $validated['venue'];
        $publication->custom_venue = $validated['venue'] === 'nueva' ? $validated['custom_venue'] : null;
        $publication->content = $validated['content'];
        $publication->description = $validated['content'];
        $publication->published_at = $isActive ? ($validated['published_at'] ?? now()->toDateString()) : null;
        $publication->is_active = $isActive;
        $publication->save();

        return back()->with('status', 'Publicación actualizada correctamente.');
    }
    
    public function destroy(Publication $publication): RedirectResponse
        {
            if ($publication->image_path) {
                Storage::disk('public')->delete($publication->image_path);
            }

            $publication->delete();

            return redirect()
                ->route('publications.index')
                ->with('status', 'Publicación eliminada correctamente.');
        }
    }