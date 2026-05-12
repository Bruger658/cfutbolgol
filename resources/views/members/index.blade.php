<x-layouts.app :title="__('Socios')">
    <div class="mx-auto max-w-6xl p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Socios</h1>
                <p class="text-on-surface-variant">Listado principal con número de socio, nombre y estado de cuotas.</p>
            </div>
            <a href="{{ route('members.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white">Nueva socia</a>
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
                    <td class="px-4 py-2">{{ $member->is_up_to_date ? 'Sí' : 'No' }}</td>
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