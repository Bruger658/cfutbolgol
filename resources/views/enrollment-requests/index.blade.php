<x-layouts.app>
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inscripciones pendientes</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Gestiona las solicitudes recibidas desde el formulario público.</p>
        </div>

        <form method="GET" action="{{ route('enrollment-requests.index') }}" class="flex items-end gap-3">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Estado</label>
                <select id="status" name="status" class="mt-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">Todos</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Filtrar</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jugador</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Contacto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Categoría</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Fechas</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($enrollmentRequests as $requestItem)
                        <tr>
                            <td class="px-4 py-4 align-top">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $requestItem->player_name }}</p>
                                <p class="text-sm text-gray-500">Nac. {{ $requestItem->birth_date->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-400">Recibida {{ $requestItem->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-4 py-4 align-top text-sm text-gray-700 dark:text-gray-200">
                                <p>{{ $requestItem->guardian_email }}</p>
                                <p>{{ $requestItem->contact_phone }}</p>
                                <a class="mt-2 inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700 hover:bg-green-100" href="{{ $requestItem->whatsappUrl() }}" target="_blank" rel="noopener">WhatsApp</a>
                            </td>
                            <td class="px-4 py-4 align-top text-sm text-gray-700 dark:text-gray-200">{{ $requestItem->category }}</td>
                            <td class="px-4 py-4 align-top">
                                <form method="POST" action="{{ route('enrollment-requests.update', $requestItem) }}" class="flex flex-col gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="rounded-md border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                        @foreach ($statuses as $value => $label)
                                            <option value="{{ $value }}" @selected($requestItem->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button class="rounded-md bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700 dark:bg-blue-600 dark:hover:bg-blue-700">Actualizar</button>
                                </form>
                            </td>
                            <td class="px-4 py-4 align-top text-xs text-gray-500 dark:text-gray-300">
                                <p>Contactado: {{ $requestItem->contacted_at?->format('d/m/Y H:i') ?? '—' }}</p>
                                <p>Prueba: {{ $requestItem->trial_scheduled_at?->format('d/m/Y H:i') ?? '—' }}</p>
                                <p>Inscripto: {{ $requestItem->enrolled_at?->format('d/m/Y H:i') ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-4 text-right align-top">
                                <form method="POST" action="{{ route('enrollment-requests.destroy', $requestItem) }}" data-confirm-delete data-confirm-message="¿Seguro que deseas eliminar esta solicitud?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm font-semibold text-red-600 hover:text-red-800">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">No hay solicitudes para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
            {{ $enrollmentRequests->links() }}
        </div>
    </div>
</x-layouts.app>