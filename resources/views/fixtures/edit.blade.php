<x-layouts.app :title="__('Editar fixture')">
    <div class="mx-auto max-w-4xl p-6">
        <h1 class="text-2xl font-semibold mb-4">Editar fixture</h1>
        <form method="POST" action="{{ route('fixtures.update', $fixture) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('fixtures._form')
        </form>
    </div>
</x-layouts.app>