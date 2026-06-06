<x-layouts.app :title="__('Staff')">
    <div class="mx-auto max-w-6xl p-6">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Staff</h1>
                <p class="text-on-surface-variant">Gestiona el equipo técnico y administrativo visible en la web.</p>
            </div>
            <a href="{{ route('staff.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white">Nuevo integrante</a>
        </div>

        @if(session('status'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-700">{{ session('status') }}</div>
        @endif

        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Orden</th>
                    <th class="px-4 py-2 text-left">Integrante</th>
                    <th class="px-4 py-2 text-left">Rol</th>
                    <th class="px-4 py-2 text-left">Categoría</th>
                    <th class="px-4 py-2 text-left">Estado</th>
                    <th class="px-4 py-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($staffMembers as $member)
                    <tr>
                        <td class="px-4 py-2">{{ $member->display_order }}</td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-3">
                                @if($member->photo_path)
                                    <img src="{{ asset('storage/' . $member->photo_path) }}" class="h-12 w-12 rounded-full object-cover" alt="Foto de {{ $member->name }}">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary font-bold">{{ mb_substr($member->name, 0, 1) }}</div>
                                @endif
                                <div>
                                    <p class="font-semibold">{{ $member->name }}</p>
                                    <p class="text-xs text-on-surface-variant">{{ $member->email ?: 'Sin email' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2">{{ $member->role }}</td>
                        <td class="px-4 py-2">{{ $member->category ?: '-' }}</td>
                        <td class="px-4 py-2">{{ $member->is_active ? 'Activo' : 'Inactivo' }}</td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('staff.edit', $member) }}" class="text-blue-600">Editar</a>
                                <form action="{{ route('staff.destroy', $member) }}" method="POST" data-confirm-delete data-confirm-message="¿Seguro que deseas borrar este integrante del staff?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600">Borrar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No hay integrantes del staff cargados.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $staffMembers->links() }}</div>
    </div>
</x-layouts.app>