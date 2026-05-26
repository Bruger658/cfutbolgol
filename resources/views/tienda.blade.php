-1

<x-layouts.base>
    <main class="pt-20 pb-24 px-4 md:px-8 max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-5xl font-black font-lexend tracking-tighter text-primary uppercase leading-none">Tienda</h1>
            <div class="h-2 w-24 bg-secondary mt-2 rounded-full"></div>
        </div>

        <div class="mt-4">
            <a href="{{ route('index') }}" class="inline-flex items-center text-sm font-medium text-sky-700 hover:text-sky-900">← Volver a la página principal</a>
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($products as $product)
                <article class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-lg border border-primary/10">
                    <img class="w-full h-52 object-cover"
                        src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1511886929837-354d827aae26?auto=format&fit=crop&w=1200&q=80' }}"
                        alt="{{ $product->name }}">
                    <div class="p-6 space-y-3">
                        <p class="text-xs uppercase tracking-wider text-on-surface-variant">{{ $product->category }}</p>
                        <p class="text-xs uppercase tracking-wider text-on-surface-variant">Talle: {{ $product->size ?? 'Sin talle' }}</p>
                        <h2 class="text-2xl font-black text-primary">{{ $product->name }}</h2>
                        <p class="text-sm text-on-surface-variant">{{ $product->description }}</p>
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-xl font-black text-on-surface">${{ number_format((float) $product->price, 2, ',', '.') }}</span>
                            <span class="text-sm font-bold {{ $product->stock > 0 ? 'text-green-700' : 'text-red-600' }}">Stock: {{ $product->stock }}</span>
                        </div>
                         <form method="POST" action="{{ route('products.checkout.store', $product) }}" class="pt-2">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full text-sm px-4 py-2 rounded-xl bg-sky-600 text-white hover:bg-sky-700 transition-colors font-bold">
                                Pagar con Mercado Pago
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <p class="text-on-surface-variant">No hay productos disponibles en este momento.</p>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $products->links() }}
        </div>
        <div class="pt-2">
            <a href="{{ route('index') }}" class="inline-flex items-center text-sm font-medium text-sky-700 hover:text-sky-900">← Volver a la página principal</a>
        </div>
    </main>
</x-layouts.base>