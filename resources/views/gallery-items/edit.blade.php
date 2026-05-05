<x-layouts.app :title="__('Editar foto')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Editar foto</h1>

        <form action="{{ route('gallery-items.update', $galleryItem) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            @include('gallery-items.form')
        </form>
    </div>
</x-layouts.app>
