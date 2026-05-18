<x-layouts.app>
    <div class="max-w-3xl mx-auto space-y-6">
        <h1 class="text-2xl font-semibold">Nuevo producto deportivo</h1>

        @if ($errors->any())
            <div class="p-4 rounded border border-red-300 bg-red-50 text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @include('products._form')
            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Guardar producto</button>
                <a href="{{ route('products.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
            </div>
        </form>
    </div>
</x-layouts.app>