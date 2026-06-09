<?php

use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function cartProduct(array $attributes = []): Product
{
    return Product::create(array_merge([
        'name' => 'Camiseta CFG',
        'category' => 'Indumentaria',
        'description' => 'Camiseta oficial del club.',
        'price' => 15000,
        'stock' => 10,
    ], $attributes));
}

it('adds products to the cart and updates quantities', function () {
    $product = cartProduct();

    $this->post(route('cart.store', $product), ['quantity' => 2])
        ->assertSessionHas('cart.'.$product->id, 2);

    $this->patch(route('cart.update', $product), ['quantity' => 4])
        ->assertSessionHas('cart.'.$product->id, 4);

    $this->get(route('cart.show'))
        ->assertOk()
        ->assertSee('Camiseta CFG')
        ->assertSee('Pagar carrito con Mercado Pago');
});

it('creates one Mercado Pago preference with every cart item', function () {
    config()->set('services.mercado_pago.access_token', 'test-token');

    Http::fake([
        'api.mercadopago.com/checkout/preferences' => Http::response([
            'id' => 'preference-123',
            'init_point' => 'https://mercadopago.test/checkout/preference-123',
        ]),
    ]);

    $shirt = cartProduct();
    $shorts = cartProduct([
        'name' => 'Short CFG',
        'price' => 9000,
        'stock' => 5,
    ]);

    $response = $this->withSession([
        'cart' => [$shirt->id => 2, $shorts->id => 1],
    ])->post(route('cart.checkout'), [
        'delivery_method' => 'pickup',
    ]);

    $response->assertRedirect('https://mercadopago.test/checkout/preference-123');

    $orders = ProductOrder::query()->orderBy('id')->get();

    expect($orders)->toHaveCount(2)
        ->and($orders->pluck('checkout_group')->unique())->toHaveCount(1)
        ->and($orders->sum('total_price'))->toEqual(39000.0)
        ->and($orders->pluck('delivery_method')->unique()->all())->toBe(['pickup']);

    Http::assertSent(function (Request $request): bool {
        $items = $request->data()['items'];

        return $request->url() === 'https://api.mercadopago.com/checkout/preferences'
            && count($items) === 2
            && $items[0]['title'] === 'Camiseta CFG'
            && $items[0]['quantity'] === 2
            && $items[1]['title'] === 'Short CFG'
            && $request->data()['external_reference'] === ProductOrder::first()->checkout_group;
    });
});

it('marks every cart order as paid and decrements stock through the webhook', function () {
    config()->set('services.mercado_pago.access_token', 'test-token');

    $shirt = cartProduct(['stock' => 4]);
    $shorts = cartProduct(['name' => 'Short CFG', 'stock' => 3]);
    $checkoutGroup = '9fc7e136-22ea-47ae-89ee-a62586b03711';

    foreach ([[$shirt, 2], [$shorts, 1]] as [$product, $quantity]) {
        ProductOrder::create([
            'product_id' => $product->id,
            'checkout_group' => $checkoutGroup,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'total_price' => (float) $product->price * $quantity,
            'status' => 'pending',
            'payment_provider' => 'mercado_pago',
            'delivery_method' => 'shipping',
        ]);
    }

    Http::fake([
        'api.mercadopago.com/v1/payments/payment-456' => Http::response([
            'id' => 'payment-456',
            'status' => 'approved',
            'external_reference' => $checkoutGroup,
        ]),
    ]);

    $this->postJson(route('products.checkout.webhook'), [
        'data' => ['id' => 'payment-456'],
    ])->assertOk();

    expect(ProductOrder::query()->where('status', 'paid')->count())->toBe(2)
        ->and($shirt->fresh()->stock)->toBe(2)
        ->and($shorts->fresh()->stock)->toBe(2);
});