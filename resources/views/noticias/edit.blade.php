<x-layouts.app :title="__('Editar noticia')">
    <form method="POST" action="{{ route('publications.update', $publication) }}" enctype="multipart/form-data" class="max-w-3xl space-y-4">
        @csrf
        @method('PUT')
        @include('noticias._form', ['publication' => $publication])
    </form>
</x-layouts.app>