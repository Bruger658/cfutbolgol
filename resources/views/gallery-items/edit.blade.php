{{-- <x-layouts.app>
    <div class="max-w-3xl">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Editar gallery item</h1>

        <form action="{{ route('gallery-items.update', $galleryItem) }}" method="POST"
            class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            @csrf
            @method('PUT')
            @include('gallery-items.form', ['galleryItem' => $galleryItem])
        </form>
    </div>
</x-layouts.app> --}}

{{-- <x-layouts.app :title="__('Editar foto')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Editar foto</h1>

        <form action="{{ route('gallery-items.update', $galleryItem) }}" method="POST" class="space-y-4">
            @method('PUT')
            @include('gallery_items.form')
        </form>
    </div>
</x-layouts.app> --}}

@include('gallery-items.edit')
<x-layouts.app :title="__('Editar foto')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Editar foto</h1>

        <form action="{{ route('gallery-items.update', $galleryItem) }}" method="POST" class="space-y-4">
            @method('PUT')
            @include('gallery_items.form')
        </form>
    </div>
</x-layouts.app>