<x-layouts.app :title="__('Nuevo usuario')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Nuevo usuario</h1>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            @include('users._form', ['isEditing' => false])
        </form>
    </div>
</x-layouts.app>