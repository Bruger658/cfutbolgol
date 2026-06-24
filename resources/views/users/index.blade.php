<x-layouts.app :title="__('Usuarios')">
    <div class="mx-auto max-w-6xl p-6">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Usuarios</h1>
                <p class="text-on-surface-variant">Administra las cuentas que pueden ingresar al panel.</p>
            </div>
            <a href="{{ route('users.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white">Nuevo usuario</a>
        </div>

        @if(session('status'))
             <div
                x-data="{ showStatusMessage: true }"
                x-init="setTimeout(() => showStatusMessage = false, 5000)"
                x-show="showStatusMessage"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-700"
            >{{ session('status') }}</div>
        @endif

        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Nombre</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Rol</th>
                    <th class="px-4 py-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-2 font-semibold">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">{{ $user->roleLabel() }}</td>
                        <td class="px-4 py-2">
                            <div class="flex gap-3">
                                <a class="text-blue-600" href="{{ route('users.edit', $user) }}">Editar</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" data-confirm-delete data-confirm-message="¿Seguro que deseas borrar este usuario?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600" @disabled(auth()->user()->is($user))>Borrar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No hay usuarios cargados.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-layouts.app>