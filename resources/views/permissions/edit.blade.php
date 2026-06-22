<x-layouts.app :title="__('Editar permiso')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Editar permiso</h1>
        <form action="{{ route('permissions.update', $permission) }}" method="POST">@csrf @method('PUT')
            @include('permissions._form')</form>
    </div>
</x-layouts.app>
