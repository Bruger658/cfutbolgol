<x-layouts.app :title="__('Galería')">
    <div class="mx-auto max-w-5xl p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Galería</h1>
            <a href="{{ route('gallery-items.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white">Nueva foto</a>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Título</th>                    
                    <th class="px-4 py-2 text-left">Miniatura</th>
                    <th class="px-4 py-2 text-left">Estado</th>
                    <th class="px-4 py-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($items as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item->title }}</td>  
                        <td class="px-4 py-2">
                            @if ($item->image_path)
                                <img
                                    src="{{ asset('storage/' . $item->image_path) }}"
                                    alt="Miniatura de {{ $item->title }}"
                                    class="h-12 w-12 rounded object-cover"
                                >
                            @else
                                <span class="text-sm text-gray-400">Sin imagen</span>
                            @endif
                        </td>                                               
                        <td class="px-4 py-2">{{ $item->is_active ? 'Activa' : 'Inactiva' }}</td>
                        <td class="px-4 py-2">
                            
                            <div class="flex items-center gap-3">
                                <a href="{{ route('gallery-items.edit', $item) }}" class="text-blue-600">Editar</a>
                                <form action="{{ route('gallery-items.destroy', $item) }}" method="POST" data-confirm-delete data-confirm-message="¿Seguro que deseas borrar esta foto?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700">Borrar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                       <td colspan="5" class="px-4 py-6 text-center text-gray-500">No hay fotos cargadas.</td>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>