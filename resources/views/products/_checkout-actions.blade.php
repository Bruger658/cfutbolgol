@if ($product->stock > 0)
    <div class="space-y-2">
        <a href="{{ route('products.checkout.prepare', $product) }}" class="block w-full text-center text-sm px-4 py-2 rounded-xl bg-sky-600 text-white hover:bg-sky-700 transition-colors font-bold">
            Comprar con Mercado Pago
        </a>        
    </div>
@else
    <button type="button" disabled class="block w-full text-center text-sm px-4 py-2 rounded-xl bg-slate-300 text-slate-600 font-bold cursor-not-allowed">
        Sin stock disponible
    </button>
@endif