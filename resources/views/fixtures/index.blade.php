<x-layouts.app :title="__('Fixture')">
    <div class="mx-auto max-w-6xl p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Fixture</h1>
                <p class="text-on-surface-variant">Gestiona los próximos partidos del club.</p>
            </div>
            <a href="{{ route('fixtures.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white">Nuevo fixture</a>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead><tr><th class="px-4 py-2 text-left">Categoría</th><th class="px-4 py-2 text-left">Partido</th><th class="px-4 py-2 text-left">Fecha y hora</th><th class="px-4 py-2 text-left">Sede</th><th class="px-4 py-2 text-left">Acciones</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($fixtures as $fixture)
                <tr>
                    <td class="px-4 py-2">{{ strtoupper($fixture->category) }}</td>
                    <td class="px-4 py-2">{{ $fixture->home_team_name }} vs {{ $fixture->away_team_name }}</td>
                    <td class="px-4 py-2">{{ $fixture->weekday }} - {{ $fixture->fixture_date->format('d/m/Y') }} {{ \Illuminate\Support\Carbon::parse($fixture->match_time)->format('H:i') }}</td>
                    <td class="px-4 py-2">{{ $fixture->venue_name }}</td>
                    <td class="px-4 py-2"><a class="text-blue-600" href="{{ route('fixtures.edit', $fixture) }}">Editar</a>
                    <form action="{{ route('fixtures.destroy', $fixture) }}" method="POST" class="inline" data-confirm-delete data-confirm-message="¿Seguro que deseas borrar este fixture?">@csrf @method('DELETE')<button class="text-red-600 ml-2">Borrar</button></form></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Sin fixtures cargados.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $fixtures->links() }}</div>
    </div>
</x-layouts.app>