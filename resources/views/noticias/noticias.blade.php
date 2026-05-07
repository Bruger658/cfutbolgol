<x-layouts.app :title="__('Noticias')">
    <div class="mx-auto max-w-5xl p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Noticias</h1>
                <p class="text-on-surface-variant">Gestiona las noticias que se muestran en la portada.</p>
            </div>
            <a href="{{ route('publications.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white">Nueva noticia</a>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Título</th>
                    <th class="px-4 py-2 text-left">Categoría</th>
                    <th class="px-4 py-2 text-left">Activo</th>
                    <th class="px-4 py-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($publications as $publication)
                    <tr>
                        <td class="px-4 py-2">{{ $publication->title }}</td>
                        <td class="px-4 py-2">{{ str_replace('_', ' ', $publication->category) }}</td>
                        <td class="px-4 py-2">{{ $publication->is_active ? 'Sí' : 'No' }}</td>
                        <td class="px-4 py-2"><a class="text-blue-600" href="{{ route('publications.edit', $publication) }}">Editar</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Sin noticias cargadas.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $publications->links() }}
        </div>
    </div>
</x-layouts.app>