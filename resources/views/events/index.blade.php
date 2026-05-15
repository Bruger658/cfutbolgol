<x-layouts.app :title="__('Calendario')">
    <div class="mx-auto max-w-6xl p-6 space-y-4">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold">Calendario de eventos</h1>
            <a href="{{ route('events.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white">Nuevo evento</a>
        </div>
        <form method="GET" class="flex gap-3 items-end">
            <div><label class="text-sm">Vista</label><select name="view" class="border rounded px-2 py-1"><option value="month" @selected($viewMode==='month')>Mensual</option><option value="week" @selected($viewMode==='week')>Semanal</option><option value="day" @selected($viewMode==='day')>Diaria</option></select></div>
            <div><label class="text-sm">Fecha base</label><input type="date" name="date" value="{{ $baseDate->toDateString() }}" class="border rounded px-2 py-1"></div>
            <label><input type="checkbox" name="show_completed" value="1" @checked($showCompleted)> Ver realizados</label>
            <button class="rounded bg-gray-800 text-white px-3 py-1">Aplicar</button>
        </form>
        <p class="text-sm text-gray-500">Rango: {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</p>
        <div class="space-y-3">
            @forelse($events as $event)
            <div class="border rounded p-4 flex justify-between items-start">
                <div><p class="font-semibold">{{ $event->title }}</p><p class="text-sm">{{ $event->starts_at->format('d/m/Y H:i') }}</p><p class="text-sm text-gray-600">{{ $event->description }}</p></div>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('events.toggle', $event) }}">@csrf @method('PATCH')<button class="text-xs px-2 py-1 rounded {{ $event->is_completed ? 'bg-green-100' : 'bg-yellow-100' }}">{{ $event->is_completed ? 'Reactivar' : 'Marcar hecho' }}</button></form>
                    <a href="{{ route('events.edit', $event) }}" class="text-blue-600">Editar</a>
                    <form action="{{ route('events.destroy', $event) }}" method="POST" data-confirm-delete data-confirm-message="¿Seguro que deseas borrar este evento?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-700">Borrar</button>
                    </form>
                </div>
            </div>
            @empty
            <p>No hay eventos para este rango.</p>
            @endforelse
        </div>
    </div>
</x-layouts.app>