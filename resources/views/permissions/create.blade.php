<x-layouts.app :title="__('Nuevo permiso')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Nuevo permiso</h1>
        <form action="{{ route('permissions.store') }}" method="POST">@csrf @include('permissions._form')</form>
    </div>
</x-layouts.app>
