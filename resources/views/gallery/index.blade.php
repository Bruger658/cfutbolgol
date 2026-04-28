Nuevo
+73
-0

<x-layouts.app :title="__('Galería')">
    <div class="space-y-8 p-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Galería</h1>
            <p class="text-sm text-slate-600">Gestiona imágenes y su estado de publicación.</p>
        </div>

        @if (session('status'))
            <div class="rounded-md bg-green-50 p-3 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('gallery-items.store') }}" class="grid gap-4 rounded-xl border p-4 md:grid-cols-2">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium" for="title">Título</label>
                <input id="title" name="title" type="text" class="w-full rounded-md border px-3 py-2" required>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium" for="image_url">URL de imagen</label>
                <input id="image_url" name="image_url" type="url" class="w-full rounded-md border px-3 py-2" required>
            </div>

            <div class="md:col-span-2">
                <label class="inline-flex items-center gap-2 text-sm font-medium">
                    <input type="checkbox" name="is_active" value="1" checked>
                    Activo
                </label>
            </div>

            <div class="md:col-span-2">
                <button class="rounded-md bg-brand-blue px-4 py-2 text-white" type="submit">Agregar imagen</button>
            </div>
        </form>

        <div class="overflow-x-auto rounded-xl border">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Título</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">URL</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600">Activo</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse ($galleryItems as $item)
                        <tr>
                            <td class="px-4 py-3 text-sm text-slate-900">{{ $item->title }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600 break-all">{{ $item->image_url }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="rounded px-2 py-1 text-xs {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $item->is_active ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('gallery-items.destroy', $item) }}" onsubmit="return confirm('¿Eliminar este elemento?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm font-medium text-red-600" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No hay elementos todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>

{{-- <x-layouts.app>
    <div class="space-y-6" x-data="{ showCreateForm: false }">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestión de galería</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Administra las imágenes del carrusel.</p>
            </div>

            <button
                type="button"
                @click="showCreateForm = !showCreateForm"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
            >
                <span class="material-symbols-outlined text-base">add</span>
                Crear
            </button>
        </div>

        <div x-show="showCreateForm" x-transition class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Nueva imagen</h2>
            <form method="POST" action="{{ route('gallery.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Título</label>
                    <input id="title" name="title" value="{{ old('title') }}" required class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                </div>
                <div class="md:col-span-2">
                    <label for="image_url" class="block text-sm font-medium text-gray-700 dark:text-gray-200">URL de imagen</label>
                    <input id="image_url" name="image_url" value="{{ old('image_url') }}" required class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                </div>
                <div class="md:col-span-3 flex justify-end">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Guardar</button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Imagen</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Título</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($galleryItems as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-14 w-20 rounded object-cover border border-gray-200 dark:border-gray-700">
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-100">{{ $item->title }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-2" x-data="{ showEdit: false }">
                                    <button type="button" @click="showEdit = !showEdit" class="rounded-md border border-amber-300 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-100">Editar</button>
                                    <form method="POST" action="{{ route('gallery.destroy', $item) }}" onsubmit="return confirm('¿Seguro que quieres borrar esta imagen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md border border-red-300 bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">Borrar</button>
                                    </form>

                                    <div x-show="showEdit" x-transition class="mt-3 w-full rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                                        <form method="POST" action="{{ route('gallery.update', $item) }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            @csrf
                                            @method('PUT')
                                            <input name="title" value="{{ $item->title }}" required class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                                            <input name="image_url" value="{{ $item->image_url }}" required class="md:col-span-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                                            <div class="md:col-span-3 flex justify-end">
                                                <button type="submit" class="rounded-md bg-amber-500 px-3 py-1 text-xs font-semibold text-white hover:bg-amber-600">Actualizar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No hay imágenes cargadas aún.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $galleryItems->links() }}
        </div>
    </div>
</x-layouts.app> --}}