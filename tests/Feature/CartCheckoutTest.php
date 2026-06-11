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

it('adds every checked product to the cart with its selected quantity', function () {
    $shirt = cartProduct();
    $shorts = cartProduct([
        'name' => 'Short CFG',
        'price' => 9000,
        'stock' => 5,
    ]);

    $this->post(route('cart.store-many'), [
        'selected_products' => [$shirt->id, $shorts->id],
        'quantities' => [
            $shirt->id => 2,
            $shorts->id => 3,
        ],
    ])->assertRedirect(route('cart.show'))
        ->assertSessionHas('cart.'.$shirt->id, 2)
        ->assertSessionHas('cart.'.$shorts->id, 3);

    $this->get(route('cart.show'))
        ->assertOk()
        ->assertSee('Productos seleccionados')
        ->assertSee('$57.000,00')
        ->assertSee('Cancelar compra y vaciar carrito');
});

it('keeps the public store route separate from product administration', function () {
    expect(route('tienda.index'))->toEndWith('/tienda')
        ->and(route('products.index'))->toEndWith('/products');

    $this->get(route('tienda.index'))->assertOk();
    $this->get(route('products.index'))->assertRedirect(route('login'));
});

it('requires at least one checked product', function () {
    $this->from(route('tienda.index'))
        ->post(route('cart.store-many'), [
            'selected_products' => [],
        ])
        ->assertRedirect(route('tienda.index'))
        ->assertSessionHasErrors('selected_products');
});

it('keeps store and cart notifications visible for five seconds', function () {
    $product = cartProduct();

    $this->withSession(['status' => 'Producto agregado al carrito.'])
        ->get(route('tienda.index'))
        ->assertOk()
        ->assertSee('data-auto-dismiss="5000"', false)
        ->assertSee("document.querySelectorAll('[data-auto-dismiss]')", false);

    $this->withSession(['status' => 'Producto agregado al carrito.'])
        ->get(route('index'))
        ->assertOk()
        ->assertSee('data-auto-dismiss="5000"', false);

    $this->withSession([
        'cart' => [$product->id => 1],
        'status' => 'Carrito actualizado.',
    ])->get(route('cart.show'))
        ->assertOk()
        ->assertSee('data-auto-dismiss="5000"', false);
});

it('can cancel the purchase and clear the cart before payment', function () {
    $product = cartProduct();

    $this->withSession([
        'cart' => [$product->id => 2],
        'pending_cart_checkout' => 'checkout-reference',
    ])->delete(route('cart.clear'))
        ->assertRedirect(route('tienda.index'))
        ->assertSessionMissing('cart')
        ->assertSessionMissing('pending_cart_checkout')
        ->assertSessionHas('status', 'Cancelaste la compra y vaciaste el carrito.');
});


it('offers cart controls on featured products and multi-selection in the full store modal', function () {
    $shirt = cartProduct();
    $shorts = cartProduct([
        'name' => 'Short CFG',
        'price' => 9000,
        'stock' => 5,
    ]);

    $this->withSession(['cart' => [$shirt->id => 2]])
        ->get(route('index'))
        ->assertOk()
        ->assertSee('Ver carrito')
        ->assertSee('Carrito (2)')
        ->assertSee('Guardar')
        ->assertSee('Tienda completa')
        ->assertSee('name="selected_products[]"', false)
        ->assertSee('value="'.$shirt->id.'"', false)
        ->assertSee('value="'.$shorts->id.'"', false)
        ->assertSee('Agregar seleccionados y ver carrito');
});

it('only exposes checkout creation through the cart', function () {
    $product = cartProduct();

    $this->get('/products/'.$product->id.'/checkout/prepare')->assertNotFound();
    $this->post('/products/'.$product->id.'/checkout')->assertNotFound();
});