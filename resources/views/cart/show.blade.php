<x-layouts.base>
    <main class="pt-24 pb-20 px-4 md:px-8 max-w-5xl mx-auto">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-4xl font-black font-lexend tracking-tighter text-primary uppercase leading-none">Carrito de compras</h1>
                <p class="mt-2 text-sm text-on-surface-variant">Revisá las cantidades, elegí la forma de entrega y pagá
                    todo junto de forma segura.</p>
            </div>
            <a href="{{ route('tienda.index') }}"
                class="rounded-xl border border-primary/20 px-4 py-3 text-center font-bold text-primary hover:bg-primary hover:text-white">Seguir
                comprando</a>
        </div>        

        @if (session('status'))
             <div data-auto-dismiss="5000" role="status" class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-700">
                {{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div data-auto-dismiss="5000" role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                {{ $errors->first() }}</div>
        @endif

        @if ($items->isEmpty())
            <div class="rounded-3xl border border-primary/10 bg-white p-8 text-center shadow-lg">
                <span class="material-symbols-outlined text-5xl text-primary">shopping_cart</span>
                <h2 class="mt-4 text-2xl font-black text-primary">Tu carrito está vacío</h2>
                <p class="mt-2 text-sm text-on-surface-variant">Agregá productos desde la tienda para armar tu pedido.
                </p>
                <a href="{{ route('tienda.index') }}"
                    class="mt-6 inline-flex rounded-xl bg-primary px-5 py-3 font-black text-on-primary">Ir a la
                    tienda</a>
            </div>
        @else
            <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
                <section class="space-y-4">
                    @foreach ($items as $item)
                        @php($product = $item['product'])
                        <article
                            class="grid gap-4 rounded-3xl border border-primary/10 bg-white p-4 shadow sm:grid-cols-[120px_1fr]">
                            <img src="{{ $product->gallery->first() ?: 'https://images.unsplash.com/photo-1511886929837-354d827aae26?auto=format&fit=crop&w=900&q=80' }}"
                                alt="{{ $product->name }}" class="h-32 w-full rounded-2xl object-cover sm:h-full">
                            <div class="space-y-3">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">
                                            {{ $product->category }} · Talle {{ $product->size ?? 'Sin talle' }}</p>
                                        <h2 class="text-xl font-black text-primary">{{ $product->name }}</h2>
                                    </div>
                                    <p class="text-lg font-black">${{ number_format($item['subtotal'], 2, ',', '.') }}
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <form method="POST" action="{{ route('cart.update', $product) }}"
                                        class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <label for="quantity-{{ $product->id }}"
                                            class="text-sm font-bold">Cantidad</label>
                                        <input id="quantity-{{ $product->id }}" type="number" name="quantity"
                                            min="0" max="{{ $product->stock }}" value="{{ $item['quantity'] }}"
                                            class="w-20 rounded-xl border-primary/20 text-sm">
                                        <button type="submit"
                                            class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-bold text-white">Actualizar</button>
                                    </form>

                                    <div class="flex flex-wrap gap-2">
                                        
                                        <form method="POST" action="{{ route('cart.destroy', $product) }}">
                                            @csrf
                                            @method('DELETE')
                                              <button type="submit"
                                                class="rounded-xl border border-red-200 px-3 py-2 text-sm font-bold text-red-600">Quitar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </section>

                <aside class="h-fit rounded-3xl border border-primary/10 bg-brand-pale p-6 shadow-lg">
                    <p class="text-xs font-black uppercase tracking-[0.25em] text-secondary">Resumen</p>
                    <div class="mt-4 space-y-3 border-b border-primary/10 pb-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-bold">Productos seleccionados</span>
                            <span class="font-black text-primary">{{ $itemCount }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-bold">Total</span>
                            <span
                                class="text-2xl font-black text-primary">${{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('cart.checkout') }}" class="mt-5 space-y-4">
                        @csrf
                        <fieldset>
                            <legend class="text-sm font-black text-primary">¿Cómo querés recibir tu compra?</legend>
                            <div class="mt-3 space-y-2 text-sm">
                                <label
                                    class="flex cursor-pointer items-start gap-2 rounded-xl border border-primary/10 bg-white p-3">
                                    <input type="radio" name="delivery_method" value="shipping"
                                        @checked(old('delivery_method', 'shipping') === 'shipping') class="mt-1">
                                    <span><strong>Coordinar envío</strong><br><span class="text-on-surface-variant">La
                                            sede se comunica para coordinar.</span></span>
                                </label>
                                <label
                                    class="flex cursor-pointer items-start gap-2 rounded-xl border border-primary/10 bg-white p-3">
                                    <input type="radio" name="delivery_method" value="pickup"
                                        @checked(old('delivery_method') === 'pickup') class="mt-1">
                                    <span><strong>Retiro en sede</strong><br><span
                                            class="text-on-surface-variant">Retirás todos los productos
                                            juntos.</span></span>
                                </label>
                            </div>
                        </fieldset>
                        <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-3 font-black text-white hover:bg-sky-700">
                            <span class="material-symbols-outlined">payments</span>
                            Pagar carrito con Mercado Pago
                        </button>
                    </form>
                    <div class="mt-4 space-y-2 text-xs text-on-surface-variant">
                        <p>Vas a realizar un único pago por todos los productos.</p>
                        <p>El stock se descuenta cuando Mercado Pago aprueba la operación.</p>                    
                    </div>
                    <a href="{{ route('tienda.index') }}"
                        class="mt-4 block rounded-xl border border-primary/20 px-4 py-3 text-center font-black text-primary hover:bg-primary hover:text-white">Agregar
                        más productos</a>
                    <form method="POST" action="{{ route('cart.clear') }}" class="mt-3" data-cart-clear-form>
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full rounded-xl border border-red-200 bg-white px-4 py-3 font-black text-red-600 hover:bg-red-50">
                            Cancelar compra y vaciar carrito
                        </button>
                    </form>
                </aside>    
            </div>

            <div id="cart-clear-modal"
                class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm"
                role="dialog" aria-modal="true" aria-labelledby="cart-clear-modal-title">
                <div class="w-full max-w-md rounded-3xl border border-primary/10 bg-white p-6 shadow-2xl">
                    <div class="flex items-start gap-4">
                        <span
                            class="material-symbols-outlined rounded-2xl bg-red-50 p-3 text-3xl text-red-600">remove_shopping_cart</span>
                        <div>
                            <h2 id="cart-clear-modal-title" class="text-xl font-black text-primary">Cancelar compra</h2>
                            <p class="mt-2 text-sm leading-6 text-on-surface-variant">¿Querés cancelar esta compra y
                                vaciar todo el carrito?</p>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button type="button" data-cart-clear-cancel
                            class="rounded-xl border border-primary/20 px-4 py-3 text-sm font-black text-primary hover:bg-primary hover:text-white">
                            Seguir comprando
                        </button>
                        <button type="button" data-cart-clear-confirm
                            class="rounded-xl bg-red-600 px-4 py-3 text-sm font-black text-white shadow-lg shadow-red-600/20 hover:bg-red-700">
                            Sí, vaciar carrito
                        </button>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const clearForm = document.querySelector('[data-cart-clear-form]');
                    const modal = document.querySelector('#cart-clear-modal');
                    const cancelButton = document.querySelector('[data-cart-clear-cancel]');
                    const confirmButton = document.querySelector('[data-cart-clear-confirm]');

                    if (!clearForm || !modal || !cancelButton || !confirmButton) {
                        return;
                    }

                    const openModal = () => {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        confirmButton.focus();
                    };

                    const closeModal = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    };

                    clearForm.addEventListener('submit', (event) => {
                        if (clearForm.dataset.skipCartClearConfirm === 'true') {
                            clearForm.dataset.skipCartClearConfirm = 'false';
                            return;
                        }

                        event.preventDefault();
                        openModal();
                    });

                    cancelButton.addEventListener('click', closeModal);

                    modal.addEventListener('click', (event) => {
                        if (event.target === modal) {
                            closeModal();
                        }
                    });

                    document.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                            closeModal();
                        }
                    });

                    confirmButton.addEventListener('click', () => {
                        clearForm.dataset.skipCartClearConfirm = 'true';
                        clearForm.requestSubmit();
                        closeModal();
                    });
                });
            </script>
        @endif
    </main>
</x-layouts.base>