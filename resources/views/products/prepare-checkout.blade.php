<x-layouts.base>
    <main class="pt-24 pb-20 px-4 md:px-8 max-w-3xl mx-auto">
        <div class="rounded-3xl border border-primary/20 bg-white shadow-xl p-6 md:p-8">
            <h1 class="text-3xl font-black text-primary uppercase tracking-tight">Preparar compra</h1>
            <p class="mt-2 text-sm text-on-surface-variant">Revisá tu producto antes de ir al checkout de Mercado Pago.</p>

            @if ($errors->any())
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="mt-6 grid gap-6 md:grid-cols-[160px_1fr] items-start">
                <img class="w-full h-40 object-cover rounded-2xl"
                    src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1511886929837-354d827aae26?auto=format&fit=crop&w=900&q=80' }}"
                    alt="{{ $product->name }}">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-wider text-on-surface-variant">{{ $product->category }}</p>
                    <h2 class="text-2xl font-black text-primary">{{ $product->name }}</h2>
                    <p class="text-sm text-on-surface-variant">{{ $product->description }}</p>
                    <p class="text-sm font-bold">Stock disponible: {{ $product->stock }}</p>
                    <p class="text-xl font-black">${{ number_format((float) $product->price, 2, ',', '.') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('products.checkout.store', $product) }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="quantity" class="block text-sm font-semibold mb-1">Cantidad</label>
                    <input id="quantity" type="number" name="quantity" min="1" max="{{ $product->stock }}" value="{{ old('quantity', 1) }}"
                        class="w-24 rounded-lg border-slate-300">
                </div>

                <div class="rounded-2xl border border-primary/10 bg-brand-pale p-4 text-sm">
                    Al confirmar, se creará la orden y se descontará el stock automáticamente.
                </div>

                <button type="submit" class="w-full rounded-xl bg-sky-600 px-4 py-3 text-white font-bold hover:bg-sky-700 transition-colors">
                    Confirmar y continuar al pago
                </button>
            </form>
        </div>
    </main>
</x-layouts.base>