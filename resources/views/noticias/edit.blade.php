<x-layouts.app :title="__('Editar noticia')">
    <div class="mx-auto max-w-4xl p-6">
        <h1 class="text-2xl font-semibold mb-4">Editar noticia</h1>
        <form method="POST" action="{{ route('publications.update', $publication) }}" enctype="multipart/form-data" class="max-w-3xl space-y-4">
            @csrf
            @method('PUT')
            @include('noticias._form', ['publication' => $publication])
        </form>    
</x-layouts.app>
