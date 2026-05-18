<x-layouts.app>
    <div class="max-w-3xl mx-auto space-y-6">
        <h1 class="text-2xl font-semibold">Checkout de Tienda</h1>

        @if (session('status'))
            <div class="p-3 rounded border border-green-300 bg-green-50 text-green-700">{{ session('status') }}</div>
        @endif

        <div class="rounded-lg border bg-white p-6 space-y-4">
            <h2 class="text-lg font-semibold">Resumen de la orden #{{ $order->id }}</h2>
            <p><strong>Producto:</strong> {{ $order->product->name }}</p>
            <p><strong>Cantidad:</strong> {{ $order->quantity }}</p>
            <p><strong>Total:</strong> ${{ number_format((float) $order->total_price, 2, ',', '.') }}</p>
            <p><strong>Proveedor de pago:</strong> Mercado Libre / Mercado Pago</p>
            <p><strong>Referencia:</strong> {{ $order->provider_reference }}</p>
            <p><strong>Estado:</strong> {{ ucfirst($order->status) }}</p>

            <a href="{{ $order->checkout_url }}" target="_blank" rel="noopener"
                class="inline-flex px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                Ir a pagar con Mercado Libre
            </a>

            <p class="text-xs text-gray-500">Nota: aquí puedes reemplazar el enlace generado por la preferencia real de la API de Mercado Pago en producción.</p>
        </div>
    </div>
</x-layouts.app>