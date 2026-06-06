<x-layouts.app :title="__('Nuevo integrante del staff')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Nuevo integrante del staff</h1>
        <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('staff._form')
        </form>
    </div>
</x-layouts.app>