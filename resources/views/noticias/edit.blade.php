<x-layouts.app :title="__('Editar noticia')">
    <form method="POST" action="{{ route('publications.update', $publication) }}" enctype="multipart/form-data" class="max-w-3xl space-y-4">
        @csrf
        @method('PUT')
        @include('noticias._form', ['publication' => $publication])
    </form>

    <form method="POST" action="{{ route('publications.destroy', $publication) }}" class="max-w-3xl mt-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">Borrar noticia</button>
    </form>
</x-layouts.app>
