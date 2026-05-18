<x-layouts.app :title="__('Socios')">
    <div class="mx-auto max-w-6xl p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Socios</h1>
                <p class="text-on-surface-variant">Listado principal con número de socio, nombre y estado de cuotas.</p>
            </div>
            {{-- <a href="{{ route('members.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white">Nueva socio</a> --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('members.export.excel', request()->query()) }}" class="rounded bg-emerald-600 px-4 py-2 text-white">Exportar Excel</a>
                <a href="{{ route('members.export.pdf', request()->query()) }}" target="_blank" class="rounded bg-slate-700 px-4 py-2 text-white">Exportar PDF</a>
                <a href="{{ route('members.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white">Nueva socio</a>
            </div>
        </div>

        <div class="mb-6 rounded-3xl border border-outline/30 bg-surface p-5 shadow-sm">
            <form action="{{ route('members.index') }}" method="GET" class="grid gap-4 md:grid-cols-[1fr_auto_auto_auto] md:items-end">
                <div class="space-y-1.5">
                    <label for="search" class="ml-1 text-sm font-bold text-white-700">Buscador de socios</label>
                    <input
                        id="search"
                        name="search"
                        value="{{ $search ?? '' }}"
                         placeholder="Buscar por nombre, apellido o categoría"
                         class="w-full rounded-xl  bg-surface text-on-surface placeholder:text-on-surface-variant px-5 py-3 transition-all focus:border-brand-blue focus:ring-brand-blue"
                        type="text"
                    />
                </div>
                <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 md:pb-3">
                    <input
                        type="checkbox"
                        name="only_debtors"
                        value="1"
                        @checked($showOnlyDebtors ?? false)
                        class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary"
                    />
                    Mostrar solo socios con cuota adeudada
                </label>
                <button
                    type="submit"
                    class="rounded-xl border border-primary/20 bg-primary px-6 py-3 font-black text-on-primary shadow-lg shadow-primary/20 transition-all hover:bg-primary/90"
                >
                    Buscar
                </button>
                @if(!empty($search) || ($showOnlyDebtors ?? false))
                    <a href="{{ route('members.index') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-center font-semibold text-slate-600 transition-colors hover:bg-slate-50">Limpiar</a>
                @endif
            </form>
        </div>


        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">N° Socio (ID)</th>
                    <th class="px-4 py-2 text-left">Nombre y apellido</th>
                    <th class="px-4 py-2 text-left">Categoría</th>
                    <th class="px-4 py-2 text-left">¿Está al día?</th>
                    <th class="px-4 py-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($members as $member)
                <tr>
                    <td class="px-4 py-2">{{ $member->id }}</td>
                    <td class="px-4 py-2">{{ $member->first_name }} {{ $member->last_name }}</td>
                    <td class="px-4 py-2">{{ $member->category }}</td>
                    {{-- <td class="px-4 py-2">{{ $member->is_up_to_date ? 'Sí' : 'No' }}</td> --}}
                    <td class="px-4 py-2">
                        @if($member->is_up_to_date)
                            Sí
                        @else
                            No
                            <div class="text-xs text-red-600 mt-1">
                                Debe: {{ collect($member->missing_months)->map(fn ($month) => \Carbon\Carbon::create()->month($month)->translatedFormat('F'))->implode(', ') }}
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-2"><a class="text-blue-600" href="{{ route('members.edit', $member) }}">Editar</a>
                    <form action="{{ route('members.destroy', $member) }}" method="POST" class="inline" data-confirm-delete data-confirm-message="¿Seguro que deseas borrar esta socia?">@csrf @method('DELETE')<button class="text-red-600 ml-2">Borrar</button></form></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Sin socias cargadas.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $members->links() }}</div>
    </div>
</x-layouts.app>