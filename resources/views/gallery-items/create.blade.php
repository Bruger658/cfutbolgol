<x-layouts.app :title="__('Crear foto')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Nueva foto</h1>
        <form action="{{ route('gallery-items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @include('gallery-items.form')
        </form>  
     </div>
</x-layouts.app>


{{-- <x-layouts.app>
    <div class="max-w-3xl">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Crear gallery item</h1>

        <form action="{{ route('gallery-items.store') }}" method="POST" enctype="multipart/form-data"
            class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            @csrf
            @include('gallery-items.form')
        </form>
    </div>
</x-layouts.app> --}}



{{-- <x-layouts.app :title="__('Gallery Items')">
    <div class="p-6 space-y-6 max-w-4xl">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Pantalla principal de Galería</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Cargá una imagen desde tu disco rígido y elegí si se publica en la web.</p>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('gallery-items.store') }}" method="POST" enctype="multipart/form-data"
            class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 space-y-4">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium mb-1">Título</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" required
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2" />
            </div>

            <div>
                <label for="image" class="block text-sm font-medium mb-1">Agregar foto (desde tu disco)</label>
                <input id="image" name="image" type="file" accept="image/*" required
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2" />
            </div>

            <label class="inline-flex items-center gap-2 text-sm font-medium">
                <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active'))
                    class="rounded border-gray-300" />
                Publicar en la página web (is_active)
            </label>

            <div>
                <button type="submit"
                    class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white font-medium hover:bg-blue-700">
                    Guardar imagen
                </button>
            </div>
        </form>

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
            <h2 class="text-lg font-semibold mb-4">Imágenes cargadas</h2>
            <div class="space-y-3">
                @forelse ($galleryItems as $item)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                        <div>
                            <p class="font-medium">{{ $item->title }}</p>
                            <p class="text-xs text-gray-500">{{ $item->is_active ? 'Activa (publicada)' : 'Inactiva (oculta)' }}</p>
                        </div>
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}"
                            class="w-20 h-14 object-cover rounded" />
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Todavía no hay imágenes cargadas.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app> --}}