<x-layouts.app :title="__('Editar evento')">
<div class="mx-auto max-w-3xl p-6">
    <h1 class="text-2xl font-semibold mb-4">Editar evento</h1>
    <form action="{{ route('events.update', $event) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        @include('events._form')
        <button class="rounded bg-blue-600 text-white px-4 py-2">Actualizar</button>
    </form>
</div>
</x-layouts.app>