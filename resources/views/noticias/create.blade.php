<x-layouts.app :title="__('Nueva noticia')">
    <form method="POST" action="{{ route('publications.store') }}" enctype="multipart/form-data" class="max-w-3xl space-y-4">
        @csrf
        @include('noticias._form')
    </form>
</x-layouts.app>