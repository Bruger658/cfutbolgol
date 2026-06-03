<x-layouts.base>
    <main class="pt-24 pb-20 px-4 md:px-8 max-w-3xl mx-auto">
        <div class="rounded-3xl border border-primary/20 bg-white shadow-xl p-6 md:p-8 space-y-6">
            <div>
                <h1 class="text-3xl font-black text-primary uppercase tracking-tight">Resumen de compra</h1>
                <p class="mt-2 text-sm text-on-surface-variant">Orden #{{ $order->id }}</p>
            </div>

            <div class="rounded-2xl border border-primary/10 bg-brand-pale p-4">
                <p class="text-sm font-bold uppercase tracking-wide text-primary">Estado</p>
                <p class="mt-1 text-lg font-black">
                    @if ($order->status === 'paid')
                        Pago aprobado
                    @elseif ($order->status === 'failed')
                        Pago rechazado o cancelado
                    @else
                        Pago pendiente
                    @endif
                </p>
            </div>

             @php
                $deliveryLabel = $order->delivery_method === 'pickup' ? 'Retiro en sede' : 'Coordinar envío';
                $whatsappPhone = config('services.store.whatsapp_phone');
                $whatsappMessage = urlencode('Hola CFG, quiero confirmar mi compra #' . $order->id . ' de ' . $order->quantity . ' x ' . $order->product->name . ' por $' . number_format((float) $order->total_price, 2, ',', '.') . '. Entrega: ' . $deliveryLabel . '.');
                $whatsappUrl = $whatsappPhone ? 'https://wa.me/' . preg_replace('/\D+/', '', $whatsappPhone) . '?text=' . $whatsappMessage : 'https://wa.me/?text=' . $whatsappMessage;
            @endphp

            <div class="grid gap-4 text-sm">
                <p><strong>Producto:</strong> {{ $order->product->name }}</p>
                <p><strong>Cantidad:</strong> {{ $order->quantity }}</p>
                <p><strong>Total:</strong> ${{ number_format((float) $order->total_price, 2, ',', '.') }}</p>
                <p><strong>Entrega:</strong> {{ $deliveryLabel }}</p>
                <p><strong>Proveedor de pago:</strong> Mercado Pago</p>
                @if ($order->provider_reference)
                    <p><strong>Preferencia:</strong> {{ $order->provider_reference }}</p>
                @endif
                @if ($order->provider_payment_id)
                    <p><strong>Pago:</strong> {{ $order->provider_payment_id }}</p>
                @endif
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                @if ($order->status === 'paid')
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                        class="inline-flex justify-center rounded-xl bg-green-600 px-4 py-3 text-white font-bold hover:bg-green-700 transition-colors">
                        Confirmar por WhatsApp
                    </a>
                @elseif ($order->checkout_url)
                    <a href="{{ $order->checkout_url }}" target="_blank" rel="noopener"
                        class="inline-flex justify-center rounded-xl bg-sky-600 px-4 py-3 text-white font-bold hover:bg-sky-700 transition-colors">
                        Ir a pagar con Mercado Pago
                    </a>
                @endif
                <a href="{{ route('tienda') }}" class="inline-flex justify-center rounded-xl bg-slate-200 px-4 py-3 text-slate-800 font-bold hover:bg-slate-300 transition-colors">
                    Volver a la tienda
                </a>
            </div>
        </div>
    </main>
</x-layouts.base>