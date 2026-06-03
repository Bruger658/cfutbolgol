<x-layouts.base>
    <main class="pt-24 pb-20 px-4 md:px-8 max-w-3xl mx-auto">
        <div class="rounded-3xl border border-primary/20 bg-white shadow-xl p-6 md:p-8">
            <h1 class="text-3xl font-black text-primary uppercase tracking-tight">Preparar compra</h1>
            <p class="mt-2 text-sm text-on-surface-variant">Revisá tu producto antes de ir al checkout seguro de Mercado Pago.</p>

            @if ($errors->any())
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="mt-6 grid gap-6 md:grid-cols-[160px_1fr] items-start">
                <div class="space-y-2">
                    <img class="w-full h-40 object-cover rounded-2xl"
                        src="{{ $product->gallery->first() ?: 'https://images.unsplash.com/photo-1511886929837-354d827aae26?auto=format&fit=crop&w=900&q=80' }}"
                        alt="{{ $product->name }}">
                    @if ($product->gallery->count() > 1)
                        <div class="grid grid-cols-3 gap-2">
                            @foreach ($product->gallery->take(3) as $image)
                                <a href="{{ $image }}" target="_blank" rel="noopener" class="block overflow-hidden rounded-lg border border-primary/10">
                                    <img src="{{ $image }}" alt="Imagen de {{ $product->name }}" class="h-12 w-full object-cover">
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-wider text-on-surface-variant">{{ $product->category }}</p>
                    <p class="text-xs uppercase tracking-wider text-on-surface-variant">Talle: {{ $product->size ?? 'Sin talle' }}</p>
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

                <fieldset class="rounded-2xl border border-primary/10 bg-white p-4">
                    <legend class="px-1 text-sm font-black text-primary">Entrega</legend>
                    <div class="mt-2 grid gap-2 sm:grid-cols-2">
                        <label class="flex items-start gap-2 rounded-xl border border-primary/10 p-3 text-sm">
                            <input type="radio" name="delivery_method" value="shipping" @checked(old('delivery_method', 'shipping') === 'shipping') class="mt-1">
                            <span><strong>Coordinar envío</strong><br><span class="text-on-surface-variant">Te contactamos para definir entrega.</span></span>
                        </label>
                        <label class="flex items-start gap-2 rounded-xl border border-primary/10 p-3 text-sm">
                            <input type="radio" name="delivery_method" value="pickup" @checked(old('delivery_method') === 'pickup') class="mt-1">
                            <span><strong>Retiro en sede</strong><br><span class="text-on-surface-variant">Retirás tu compra en CFG.</span></span>
                        </label>
                    </div>
                </fieldset>


                <div class="rounded-2xl border border-primary/10 bg-brand-pale p-4 text-sm text-on-surface-variant">
                   Al confirmar, te vamos a redirigir a Mercado Pago. El stock se descuenta cuando el pago queda aprobado. Después vas a ver un botón de WhatsApp para confirmar la compra con la sede.
                </div>

                <button type="submit" class="w-full rounded-xl bg-sky-600 px-4 py-3 text-white font-bold hover:bg-sky-700 transition-colors">
                    Pagar con Mercado Pago
                </button>
                 <a href="{{ route('index') }}" class="block w-full rounded-xl border border-primary/20 px-4 py-3 text-center font-bold text-primary hover:bg-primary hover:text-white transition-colors">
                    Cancelar e ir al inicio
                </a>
            </form>
        </div>
    </main>
</x-layouts.base>