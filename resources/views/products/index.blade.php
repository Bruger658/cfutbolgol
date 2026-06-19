<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Tienda deportiva</h1>
            <a href="{{ route('products.create') }}" class="px-4 py-2 rounded bg-blue-600 text-white">Cargar producto</a>
        </div>

       

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($products as $product)
                <article class="rounded-lg border bg-white shadow-sm overflow-hidden">
                    <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1511886929837-354d827aae26?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    <div class="p-4 space-y-2">
                        <p class="text-xs uppercase tracking-wide text-gray-500">{{ $product->category }}</p>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Talle: {{ $product->size ?? 'Sin talle' }}</p>
                        <h2 class="text-lg font-semibold text-slate-900">{{ $product->name }}</h2>
                        <p class="text-sm text-gray-600">{{ $product->description }}</p>
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-xl font-extrabold text-slate-900">${{ number_format((float) $product->price, 2, ',', '.') }}</span>
                            <span class="text-sm {{ $product->stock > 0 ? 'text-green-700' : 'text-red-600' }}">Stock: {{ $product->stock }}</span>
                        </div>                       

                        <div class="flex items-center gap-2 pt-2">
                            <a href="{{ route('products.edit', $product) }}" class="text-sm px-3 py-1 rounded bg-green-600 text-white hover:bg-green-700 transition-colors">Editar</a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" data-confirm-delete data-confirm-message="¿Seguro que deseas eliminar este producto?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm px-3 py-1 rounded border border-red-300 text-red-600">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <p class="text-gray-600">Todavía no hay productos cargados.</p>
            @endforelse
        </div>

        <div>
            {{ $products->links() }}
        </div>
    </div>
</x-layouts.app>