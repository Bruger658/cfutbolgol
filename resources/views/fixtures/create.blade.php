<x-layouts.app :title="__('Crear fixture')">
    <div class="mx-auto max-w-4xl p-6">
        <h1 class="text-2xl font-semibold mb-4">Crear fixture</h1>
        <form method="POST" action="{{ route('fixtures.store') }}" enctype="multipart/form-data">
            @csrf
            @include('fixtures._form')
        </form>
    </div>
</x-layouts.app>