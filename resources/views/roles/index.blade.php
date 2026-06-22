<x-layouts.app :title="__('Roles')">
    <div class="mx-auto max-w-6xl p-6">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Roles</h1>
                <p class="text-on-surface-variant">Gestiona los perfiles de acceso y sus permisos.</p>
            </div>
            <a href="{{ route('roles.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white">Nuevo rol</a>
        </div>
        @if(session('status'))<div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-700">{{ session('status') }}</div>@endif
        <table class="min-w-full divide-y divide-gray-200">
            <thead><tr><th class="px-4 py-2 text-left">Nombre</th><th class="px-4 py-2 text-left">Slug</th><th class="px-4 py-2 text-left">Permisos</th><th class="px-4 py-2 text-left">Usuarios</th><th class="px-4 py-2 text-left">Acciones</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($roles as $role)
                    <tr><td class="px-4 py-2 font-semibold">{{ $role->name }}</td><td class="px-4 py-2">{{ $role->slug }}</td><td class="px-4 py-2">{{ $role->permissions_count }}</td><td class="px-4 py-2">{{ $role->users_count }}</td><td class="px-4 py-2"><div class="flex gap-3"><a class="text-blue-600" href="{{ route('roles.edit', $role) }}">Editar</a><form action="{{ route('roles.destroy', $role) }}" method="POST" data-confirm-delete data-confirm-message="¿Seguro que deseas borrar este rol?">@csrf @method('DELETE')<button class="text-red-600">Borrar</button></form></div></td></tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No hay roles cargados.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $roles->links() }}</div>
    </div>
</x-layouts.app>