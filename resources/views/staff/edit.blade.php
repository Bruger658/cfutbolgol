<x-layouts.app :title="__('Editar integrante del staff')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Editar integrante del staff</h1>
        <form action="{{ route('staff.update', $staff) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('staff._form')
        </form>
    </div>
</x-layouts.app>