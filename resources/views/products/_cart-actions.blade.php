@if ($product->stock > 0)
    <form method="POST" action="{{ route('cart.store', $product) }}" data-preserve-scroll class="grid grid-cols-[96px_1fr] gap-2">
        @csrf
        <label class="sr-only" for="cart-quantity-{{ $context ?? 'product' }}-{{ $product->id }}">Cantidad de {{ $product->name }}</label>
        <input id="cart-quantity-{{ $context ?? 'product' }}-{{ $product->id }}" type="number" name="quantity" value="1"
            min="1" max="{{ $product->stock }}" class="w-full rounded-xl border-primary/20 text-sm"
            aria-label="Cantidad de {{ $product->name }}">
        <button type="submit"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-3 py-2 text-sm font-black text-white transition-colors hover:bg-sky-700">
            <span class="material-symbols-outlined text-lg">add_shopping_cart</span>
            Guardar
        </button>
    </form>
@else
    <button type="button" disabled
        class="block w-full cursor-not-allowed rounded-xl bg-slate-300 px-4 py-2 text-center text-sm font-bold text-slate-600">
        Sin stock disponible
    </button>
@endif