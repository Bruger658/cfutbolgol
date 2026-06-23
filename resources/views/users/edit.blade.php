<x-layouts.app :title="__('Editar usuario')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Editar usuario</h1>
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            @include('users._form', ['isEditing' => true])
        </form>
    </div>
</x-layouts.app>