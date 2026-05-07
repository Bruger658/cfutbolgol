<x-layouts.app :title="__('Noticias')">
    <div class="space-y-6">
       <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-on-surface">Noticias</h1>
                <p class="text-on-surface-variant">Gestiona las noticias que se muestran en la portada.</p>
            </div>
            <a href="{{ route('publications.create') }}" class="px-4 py-2 rounded-lg bg-primary text-white font-bold">Nueva noticia</a>
        </div>

        <div class="bg-white rounded-xl overflow-hidden border">
            <table class="w-full text-sm">
                <thead><tr class="text-left border-b"><th class="p-3">Título</th><th>Categoría</th><th>Activo</th><th></th></tr></thead>
                <tbody>
                    @forelse($publications as $publication)
                        <tr class="border-b">
                            <td class="p-3">{{ $publication->title }}</td>
                            <td>{{ str_replace('_', ' ', $publication->category) }}</td>
                            <td>{{ $publication->is_active ? 'Sí' : 'No' }}</td>
                            <td><a class="text-primary font-bold" href="{{ route('publications.edit', $publication) }}">Editar</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-3">Sin noticias cargadas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $publications->links() }}
    </div>
</x-layouts.app>