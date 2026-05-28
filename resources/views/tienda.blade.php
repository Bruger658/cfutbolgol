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
            @forelse ($products->take(3) as $product)
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
                        {{-- <a href="{{ route('products.checkout.prepare', $product) }}" class="block w-full text-center text-sm px-4 py-2 rounded-xl bg-sky-600 text-white hover:bg-sky-700 transition-colors font-bold">
                                Pagar con Mercado Pago
                        </a> --}}
                    </div>
                </article>
            @empty
                <p class="text-on-surface-variant">No hay productos disponibles en este momento.</p>
            @endforelse
        </div>

        <div class="mt-8">
            <button id="open-full-store-page" type="button"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary text-on-primary font-bold uppercase tracking-wide">
                Ver tienda completa
                <span class="material-symbols-outlined text-lg">arrow_forward</span>
            </button>
        </div>

        <div id="full-store-page-card" class="hidden mt-10 bg-surface-container-lowest border border-primary/10 rounded-3xl p-6 md:p-8 shadow-xl">
            <div class="flex items-center justify-between gap-4 mb-6">
                <h2 class="text-3xl font-black text-primary uppercase tracking-tight">Tienda completa</h2>
                <button id="close-full-store-page" type="button" class="px-4 py-2 rounded-xl text-sm font-bold bg-slate-200 text-slate-800 hover:bg-slate-300">Cerrar</button>
            </div>

             <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse ($products as $product)
                    <article class="bg-white rounded-2xl overflow-hidden shadow border border-primary/10">
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
                             <a href="{{ route('products.checkout.prepare', $product) }}" class="block w-full text-center text-sm px-4 py-2 rounded-xl bg-sky-600 text-white hover:bg-sky-700 transition-colors font-bold">
                                Pagar con Mercado Pago
                            </a>
                        </div>
                    </article>
                @empty
                    <p class="text-on-surface-variant">No hay productos disponibles en este momento.</p>
                @endforelse
            </div>

            <div class="mt-10 rounded-2xl border border-primary/10 bg-white/95 p-4 shadow-sm">
                {{ $products->onEachSide(1)->links() }}
            </div>
        </div>

        <div class="pt-6">
            <a href="{{ route('index') }}" class="inline-flex items-center text-sm font-medium text-sky-700 hover:text-sky-900">← Volver a la página principal</a>
        </div>
    </main>
    <script>    
        document.addEventListener('DOMContentLoaded', () => {
            const openBtn = document.getElementById('open-full-store-page');
            const closeBtn = document.getElementById('close-full-store-page');
            const storeCard = document.getElementById('full-store-page-card');

            openBtn?.addEventListener('click', () => {
                storeCard?.classList.remove('hidden');
                storeCard?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });

            closeBtn?.addEventListener('click', () => {
                storeCard?.classList.add('hidden');
            });
        });
    </script>
</x-layouts.base>    