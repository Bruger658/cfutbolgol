<x-layouts.app :title="__('Nuevo rol')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Nuevo rol</h1>
        <form action="{{ route('roles.store') }}" method="POST">@csrf @include('roles._form')</form>
    </div>
</x-layouts.app>
