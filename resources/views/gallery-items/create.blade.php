<x-layouts.app :title="__('Crear foto')">
    <div class="mx-auto max-w-3xl p-6">
        <h1 class="mb-6 text-2xl font-semibold">Nueva foto</h1>

        <form action="{{ route('gallery-items.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @include('gallery-items.form')
        </form>
    </div>
</x-layouts.app>



