@if ($product->stock > 0)
    <form method="POST" action="{{ route('products.checkout.store', $product) }}" class="space-y-2">
        @csrf
        <input type="hidden" name="quantity" value="1">
        <button type="submit" class="block w-full text-center text-sm px-4 py-2 rounded-xl bg-sky-600 text-white hover:bg-sky-700 transition-colors font-bold">
            Comprar con Mercado Pago
        </button>
        <a href="{{ route('products.checkout.prepare', $product) }}" class="block w-full text-center text-xs text-sky-700 hover:text-sky-900 font-semibold">
            Elegir cantidad
        </a>
    </form>
@else
    <button type="button" disabled class="block w-full text-center text-sm px-4 py-2 rounded-xl bg-slate-300 text-slate-600 font-bold cursor-not-allowed">
        Sin stock disponible
    </button>
@endif