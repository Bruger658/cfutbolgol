<x-layouts.base>
    <main class="pt-20 pb-24 px-4 md:px-8 max-w-7xl mx-auto">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-5xl font-black font-lexend tracking-tighter text-primary uppercase leading-none">Tienda
                </h1>
                <div class="h-2 w-24 bg-secondary mt-2 rounded-full"></div>
                <a href="{{ route('index') }}"
                    class="mt-4 inline-flex items-center text-sm font-medium text-sky-700 hover:text-sky-900">← Volver a
                    la página principal</a>
            </div>
            <a href="{{ route('cart.show') }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-3 font-black uppercase tracking-wide text-on-primary shadow-lg hover:bg-sky-700">
                <span class="material-symbols-outlined">shopping_cart</span>
                Carrito
                @if ($cartCount > 0)
                    <span class="rounded-full bg-white px-2 py-0.5 text-xs text-primary">{{ $cartCount }}</span>
                @endif
            </a>
        </div>

        @if (session('status'))             
             <div data-auto-dismiss="5000" role="status" class="mt-6 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-700">
                {{ session('status') }}</div>
        @endif        
        
        @if ($errors->any())
            <div data-auto-dismiss="5000" role="alert" class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                {{ $errors->first() }}</div>
        @endif

        <section class="mt-10 rounded-3xl border border-primary/10 bg-surface-container-lowest p-5 shadow-lg">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.25em] text-secondary">Encontrá tu equipo</p>
                    <h2 class="text-3xl font-black uppercase tracking-tight text-primary">Filtros de tienda</h2>
                </div>
                 <form method="GET" action="{{ route('tienda.index') }}" class="grid gap-3 sm:grid-cols-3 lg:min-w-[680px]">
                    <label class="block text-sm font-bold text-on-surface">
                        Categoría
                        <select name="category" class="mt-1 w-full rounded-xl border-primary/20 text-sm">
                            <option value="">Todas</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-sm font-bold text-on-surface">
                        Talle
                        <select name="size" class="mt-1 w-full rounded-xl border-primary/20 text-sm">
                            <option value="">Todos</option>
                            @foreach ($sizes as $size)
                                <option value="{{ $size }}" @selected(request('size') === $size)>{{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <div class="flex items-end gap-2">
                         <button type="submit"
                            class="flex-1 rounded-xl bg-primary px-4 py-2.5 text-sm font-black text-on-primary hover:bg-sky-700">Filtrar</button>
                       <a href="{{ route('tienda.index') }}"
                            class="rounded-xl border border-primary/20 px-4 py-2.5 text-sm font-black text-primary hover:bg-primary hover:text-white">Limpiar</a>
                    </div>
                </form>
            </div>
        </section>

        <form id="bulk-cart-form" method="POST" action="{{ route('cart.store-many') }}">
            @csrf
        </form>

        <section class="mt-10">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.25em] text-secondary">Selección CFG</p>
                    <h2 class="text-3xl font-black uppercase tracking-tight text-primary">Productos destacados</h2>
                   <p class="mt-2 text-sm text-on-surface-variant">Tildá uno o varios productos y agregalos juntos al
                        carrito.</p>
                </div>
                <button id="add-selected-products" form="bulk-cart-form" type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-3 font-black text-on-primary shadow-lg transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-50">
                    <span class="material-symbols-outlined">add_shopping_cart</span>
                    <span>Agregar seleccionados</span>
                    <span id="selected-products-count"
                        class="hidden rounded-full bg-white px-2 py-0.5 text-xs text-primary">0</span>
                </button>
            </div>

            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($products as $product)
                    <article data-product-card
                        class="overflow-hidden rounded-3xl border border-primary/10 bg-white shadow-lg transition duration-200">
                        <div class="relative">
                             <label
                                class="absolute right-3 top-3 z-10 flex cursor-pointer items-center gap-2 rounded-full bg-white/95 px-3 py-2 text-sm font-black text-primary shadow-lg">
                                <input form="bulk-cart-form" data-product-checkbox type="checkbox"
                                    name="selected_products[]" value="{{ $product->id }}" @checked(in_array($product->id, old('selected_products', [])))
                                    class="h-5 w-5 rounded border-primary/30 text-primary focus:ring-primary">
                                Tildar
                            </label>
                            <img class="h-56 w-full object-cover"
                                src="{{ $product->gallery->first() ?: 'https://images.unsplash.com/photo-1511886929837-354d827aae26?auto=format&fit=crop&w=1200&q=80' }}"
                                alt="{{ $product->name }}">
                            @if ($product->store_labels)
                                <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                                    @foreach ($product->store_labels as $label)
                                        <span
                                            class="rounded-full px-3 py-1 text-xs font-black uppercase shadow {{ $label['class'] }}">{{ $label['text'] }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="p-6 space-y-4">
                            <div
                                class="flex flex-wrap gap-2 text-xs font-bold uppercase tracking-wider text-on-surface-variant">
                                <span class="rounded-full bg-brand-pale px-3 py-1">{{ $product->category }}</span>
                                <span class="rounded-full bg-slate-100 px-3 py-1">Talle:
                                    {{ $product->size ?? 'Sin talle' }}</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-primary">{{ $product->name }}</h3>
                                <p class="mt-2 line-clamp-3 text-sm text-on-surface-variant">
                                    {{ $product->description }}</p>
                            </div>

                            @if ($product->gallery->count() > 1)
                                <div class="grid grid-cols-4 gap-2" aria-label="Galería de {{ $product->name }}">
                                    @foreach ($product->gallery->take(4) as $image)
                                        <a href="{{ $image }}" target="_blank" rel="noopener"
                                            class="block overflow-hidden rounded-xl border border-primary/10">
                                            <img src="{{ $image }}" alt="Imagen de {{ $product->name }}"
                                                class="h-16 w-full object-cover transition-transform hover:scale-105">
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center justify-between border-t border-primary/10 pt-4">
                                <span
                                    class="text-2xl font-black text-on-surface">${{ number_format((float) $product->price, 2, ',', '.') }}</span>
                                <span
                                    class="text-sm font-black {{ $product->stock <= 3 ? 'text-amber-700' : 'text-green-700' }}">Stock:
                                    {{ $product->stock }}</span>
                            </div>                           
                        </div>
                    </article>
                @empty
                    <p class="text-on-surface-variant">No hay productos disponibles con esos filtros.</p>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </section>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = Array.from(document.querySelectorAll('[data-product-checkbox]'));
            const submitButton = document.getElementById('add-selected-products');
            const countBadge = document.getElementById('selected-products-count');

            const refreshSelection = () => {
                const selected = checkboxes.filter((checkbox) => checkbox.checked);

                checkboxes.forEach((checkbox) => {
                    const card = checkbox.closest('[data-product-card]');
                    card?.classList.toggle('ring-4', checkbox.checked);
                    card?.classList.toggle('ring-sky-400', checkbox.checked);
                });

                if (submitButton && countBadge) {
                    submitButton.disabled = selected.length === 0;
                    countBadge.textContent = selected.length;
                    countBadge.classList.toggle('hidden', selected.length === 0);
                }
            };

            checkboxes.forEach((checkbox) => checkbox.addEventListener('change', refreshSelection));
            refreshSelection();
        });
    </script>
</x-layouts.base>