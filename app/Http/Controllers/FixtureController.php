<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FixtureController extends Controller
{
    public function index(): View
    {
        $fixtures = Fixture::query()->latest('fixture_date')->paginate(12);

        return view('fixtures.index', compact('fixtures'));
    }

    public function create(): View
    {
        return view('fixtures.create', ['fixture' => new Fixture(['is_active' => true, 'is_home_venue' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateFixture($request, true);

        $data['home_team_badge_path'] = $request->file('home_team_badge')->store('fixtures', 'public');
        $data['away_team_badge_path'] = $request->file('away_team_badge')->store('fixtures', 'public');
        $data['is_home_venue'] = $request->boolean('is_home_venue');
        $data['is_active'] = $request->boolean('is_active');

        Fixture::create($data);

        return redirect()->route('fixtures.index')->with('status', 'Fixture creado correctamente.');
    }

    public function edit(Fixture $fixture): View
    {
        return view('fixtures.edit', compact('fixture'));
    }

    public function update(Request $request, Fixture $fixture): RedirectResponse
    {
        $data = $this->validateFixture($request, false);

        if ($request->hasFile('home_team_badge')) {
            if ($fixture->home_team_badge_path) {
                Storage::disk('public')->delete($fixture->home_team_badge_path);
            }
            $data['home_team_badge_path'] = $request->file('home_team_badge')->store('fixtures', 'public');
        }

        if ($request->hasFile('away_team_badge')) {
            if ($fixture->away_team_badge_path) {
                Storage::disk('public')->delete($fixture->away_team_badge_path);
            }
            $data['away_team_badge_path'] = $request->file('away_team_badge')->store('fixtures', 'public');
        }

        $data['is_home_venue'] = $request->boolean('is_home_venue');
        $data['is_active'] = $request->boolean('is_active');

        $fixture->update($data);

        return redirect()->route('fixtures.index')->with('status', 'Fixture actualizado correctamente.');
    }

    public function destroy(Fixture $fixture): RedirectResponse
    {
        if ($fixture->home_team_badge_path) {
            Storage::disk('public')->delete($fixture->home_team_badge_path);
        }
        if ($fixture->away_team_badge_path) {
            Storage::disk('public')->delete($fixture->away_team_badge_path);
        }

        $fixture->delete();

        return redirect()->route('fixtures.index')->with('status', 'Fixture eliminado correctamente.');
    }

    private function validateFixture(Request $request, bool $isCreate): array
    {
        return $request->validate([
            'category' => ['required', 'in:edefi,bafi,futsala,femenino'],
            'fixture_date' => ['required', 'date'],
            'home_team_name' => ['required', 'string', 'max:255'],
            'home_team_badge' => [$isCreate ? 'required' : 'nullable', 'image', 'max:4096'],
            'away_team_name' => ['required', 'string', 'max:255'],
            'away_team_badge' => [$isCreate ? 'required' : 'nullable', 'image', 'max:4096'],
            'match_time' => ['required', 'date_format:H:i'],
            'weekday' => ['required', 'string', 'max:30'],
            'venue_name' => ['required', 'string', 'max:255'],
            'is_home_venue' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}