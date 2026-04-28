<x-layouts.app>
    <div class="max-w-3xl">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Editar gallery item</h1>

        <form action="{{ route('gallery-items.update', $galleryItem) }}" method="POST"
            class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            @csrf
            @method('PUT')
            @include('gallery-items.form', ['galleryItem' => $galleryItem])
        </form>
    </div>
</x-layouts.app>