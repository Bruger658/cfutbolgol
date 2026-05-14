<x-layouts.app :title="__('Nuevo evento')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="text-2xl font-semibold mb-4">Nuevo evento</h1>
        <form action="{{ route('events.store') }}" method="POST" class="space-y-4">
            @csrf
            @include('events._form')
            <button class="rounded bg-blue-600 text-white px-4 py-2">Guardar</button>
        </form>
    </div>
</x-layouts.app>