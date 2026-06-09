<x-layouts.base>
     <main class="mx-auto max-w-3xl px-4 pb-20 pt-24 md:px-8">
        <div class="space-y-6 rounded-3xl border border-primary/20 bg-white p-6 shadow-xl md:p-8">
            <div>
                 <h1 class="text-3xl font-black uppercase tracking-tight text-primary">Resumen de compra</h1>
                <p class="mt-2 text-sm text-on-surface-variant">Pedido
                    #{{ $order->checkout_group ? Str::limit($order->checkout_group, 8, '') : $order->id }}</p>
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
                $productSummary = $orders
                    ->map(fn($item) => $item->quantity . ' x ' . $item->product->name)
                    ->implode(', ');
                $whatsappMessage = urlencode(
                    'Hola CFG, quiero confirmar mi compra #' .
                        $order->id .
                        ': ' .
                        $productSummary .
                        ' por $' .
                        number_format($total, 2, ',', '.') .
                        '. Entrega: ' .
                        $deliveryLabel .
                        '.',
                );
                $whatsappUrl = $whatsappPhone
                    ? 'https://wa.me/' . preg_replace('/\D+/', '', $whatsappPhone) . '?text=' . $whatsappMessage
                    : 'https://wa.me/?text=' . $whatsappMessage;

            @endphp 
               

             <section>
                <h2 class="text-sm font-black uppercase tracking-wide text-primary">Productos</h2>
                <div class="mt-3 divide-y divide-primary/10 rounded-2xl border border-primary/10">
                    @foreach ($orders as $item)
                        <div class="flex items-start justify-between gap-4 p-4 text-sm">
                            <div>
                                <p class="font-black text-on-surface">{{ $item->product->name }}</p>
                                <p class="text-on-surface-variant">{{ $item->quantity }} ×
                                    ${{ number_format((float) $item->unit_price, 2, ',', '.') }}</p>
                            </div>
                            <p class="font-black">${{ number_format((float) $item->total_price, 2, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <div class="grid gap-3 rounded-2xl bg-slate-50 p-4 text-sm">
                <p class="flex justify-between gap-4"><strong>Total:</strong> <span
                        class="text-xl font-black text-primary">${{ number_format($total, 2, ',', '.') }}</span></p>
                <p><strong>Entrega:</strong> {{ $deliveryLabel }}</p>
                <p><strong>Proveedor de pago:</strong> Mercado Pago</p>
                
                @if ($order->provider_payment_id)
                    <p><strong>Pago:</strong> {{ $order->provider_payment_id }}</p>
                @endif
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                @if ($order->status === 'paid')
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                      class="inline-flex justify-center rounded-xl bg-green-600 px-4 py-3 font-bold text-white transition-colors hover:bg-green-700">
                        Confirmar por WhatsApp
                    </a>
                @elseif ($order->checkout_url)
                      <a href="{{ $order->checkout_url }}"
                        class="inline-flex justify-center rounded-xl bg-sky-600 px-4 py-3 font-bold text-white transition-colors hover:bg-sky-700">
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