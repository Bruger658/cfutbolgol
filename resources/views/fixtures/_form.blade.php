@php($categories = ['edefi' => 'EDEFI', 'bafi' => 'BAFI', 'futsala' => 'Futsala', 'femenino' => 'Femenino'])

<div class="space-y-4">
    <div>
        <label>Categoría</label>
         <select name="category" class="w-full border rounded p-2 bg-primary text-on-primary" style="background-color:#0b1730;color:#ffffff;border-color:#94a3b8;" required>
            @foreach($categories as $value => $label)
                <option value="{{ $value }}" style="background-color:#0b1730;color:#ffffff;" @selected(old('category', $fixture->category ?? 'edefi') === $value)>{{ $label }}</option>
            @endforeach            
        </select>
    </div>


    <div><label>Fecha</label><input type="date" name="fixture_date" class="w-full border rounded p-2" value="{{ old('fixture_date', optional($fixture->fixture_date ?? null)->format('Y-m-d')) }}" required></div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
            <label>Escudo local</label>
            <input type="file" name="home_team_badge" class="w-full border rounded p-2" @empty($fixture->id) required @endempty>
            @if(!empty($fixture->home_team_badge_path))
                <img src="{{ asset('storage/' . $fixture->home_team_badge_path) }}" class="mt-2 h-16 w-16 object-contain" alt="Escudo local">
            @endif
            <input name="home_team_name" class="w-full border rounded p-2 mt-2" value="{{ old('home_team_name', $fixture->home_team_name ?? '') }}" placeholder="Equipo local" required>
        </div>

        <div class="text-center text-xl font-bold pb-10">VS</div>

        <div>
            <label>Escudo visitante</label>
            <input type="file" name="away_team_badge" class="w-full border rounded p-2" @empty($fixture->id) required @endempty>
            @if(!empty($fixture->away_team_badge_path))
                <img src="{{ asset('storage/' . $fixture->away_team_badge_path) }}" class="mt-2 h-16 w-16 object-contain" alt="Escudo visitante">
            @endif
            <input name="away_team_name" class="w-full border rounded p-2 mt-2" value="{{ old('away_team_name', $fixture->away_team_name ?? '') }}" placeholder="Equipo visitante" required>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label>Hora</label><input type="time" name="match_time" class="w-full border rounded p-2" value="{{ old('match_time', $fixture->match_time ? \Illuminate\Support\Carbon::parse($fixture->match_time)->format('H:i') : '') }}" required></div>
        <div>
            <label>Día de la semana</label>
            <select name="weekday" class="w-full border rounded p-2 bg-primary text-on-primary" style="background-color:#0b1730;color:#ffffff;border-color:#94a3b8;" required>
                @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $day)
                    <option value="{{ $day }}" style="background-color:#0b1730;color:#ffffff;" @selected(old('weekday', $fixture->weekday ?? '') === $day)>{{ $day }}</option>
                @endforeach
            </select>
        </div>
    </div>

     <div>
        <label>Sede</label>
        <input
            name="venue_name"
            list="fixture-venues"
            class="w-full border rounded p-2"
            value="{{ old('venue_name', $fixture->venue_name ?? 'Almafuerte') }}"
            placeholder="Escribí o elegí una sede"
            required
        >
        <datalist id="fixture-venues">
            <option value="Almafuerte"></option>
            <option value="Stylo"></option>
        </datalist>
        <p class="mt-1 text-xs text-on-surface-variant">Podés ingresar una sede nueva para partidos de local o visitante.</p>
    </div>
    <div><label><input type="checkbox" name="is_home_venue" value="1" @checked(old('is_home_venue', $fixture->is_home_venue ?? true))> Local en Almafuerte y Stylo (desmarca si es visitante)</label></div>
    <div><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $fixture->is_active ?? true))> Activo</label></div>

    <div class="flex items-center gap-3">
        <button class="rounded bg-blue-600 px-4 py-2 text-white">Guardar</button>
        <a href="{{ route('fixtures.index') }}" class="text-sm text-on-surface-variant hover:text-on-surface">Cancelar</a>
    </div>
</div>