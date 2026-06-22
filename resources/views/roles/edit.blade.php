<x-layouts.app :title="__('Editar rol')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Editar rol</h1>
        <form action="{{ route('roles.update', $role) }}" method="POST">@csrf @method('PUT')
            @include('roles._form')</form>
    </div>
</x-layouts.app>
