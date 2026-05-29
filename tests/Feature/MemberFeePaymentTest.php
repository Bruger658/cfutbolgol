<?php

use App\Models\Member;
use App\Models\MemberFeePayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;

beforeEach(function () {
    config([
        'membership.monthly_fee' => 35000,
        'membership.late_surcharge_percentage' => 10,
        'membership.late_surcharge_starts_after_day' => 10,
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

it('shows member data and late surcharge after day ten', function () {
    Carbon::setTestNow('2026-05-11 10:00:00');
    $user = User::factory()->create();

    $member = Member::create([
        'first_name' => 'Mario',
        'last_name' => 'Suarez',
        'category' => 'futsala',
        'document_number' => '30111222',
        'address' => 'Calle 10',
        'city' => 'Moron',
        'phone' => '111111',
        'responsible_adult_phone' => null,
        'paid_months' => [1, 2, 3, 4],
        'is_up_to_date' => false,
    ]);

    $response = actingAs($user)->get(route('members.fee-payments.index', ['search' => (string) $member->id]));

    $response->assertOk();
    $response->assertSee('Mario Suarez');
    $response->assertSee('Mayo');
    $response->assertSee('$35.000');
    $response->assertSee('$3.500');
    $response->assertSee('$38.500');
});

it('registers selected fee months using the existing paid months field', function () {
    Carbon::setTestNow('2026-05-09 10:00:00');
    $user = User::factory()->create();

    $member = Member::create([
        'first_name' => 'Lucia',
        'last_name' => 'Fernandez',
        'category' => 'femenino',
        'document_number' => '40111222',
        'address' => 'Calle 20',
        'city' => 'Haedo',
        'phone' => '222222',
        'responsible_adult_phone' => null,
        'paid_months' => [1, 2, 3, 4],
        'is_up_to_date' => false,
    ]);

    $response = actingAs($user)->post(route('members.fee-payments.store', $member), [
        'months' => [5],
    ]);

    $response->assertRedirect(route('members.fee-payments.index', ['member' => $member->id]));
    $response->assertSessionHas('status');

    $member->refresh();

    expect($member->paid_months)->toBe([1, 2, 3, 4, 5]);
    expect($member->is_up_to_date)->toBeTrue();
});

it('shows the public payment link in the website navigation', function () {
    $response = get(route('index'));

    $response->assertOk();
    $response->assertSee('Pagar cuota');
    $response->assertSee(route('fees.public.index'), false);
});

it('receives a public fee payment and updates the backend member record', function () {
    Carbon::setTestNow('2026-05-09 10:00:00');

    $member = Member::create([
        'first_name' => 'Sofia',
        'last_name' => 'Gomez',
        'category' => 'futsala',
        'document_number' => '35111222',
        'address' => 'Calle 30',
        'city' => 'Castelar',
        'phone' => '333333',
        'responsible_adult_phone' => null,
        'paid_months' => [1, 2, 3, 4],
        'is_up_to_date' => false,
    ]);

    $response = get(route('fees.public.index', ['search' => '35111222']));

    $response->assertOk();
    $response->assertSee('Sofia Gomez');
    $response->assertSee('Mayo');

    $response = post(route('fees.public.store', $member), [
        'months' => [5],
    ]);

    $response->assertRedirect(route('fees.public.index', ['member' => $member->id]));
    $response->assertSessionHas('status');

    $member->refresh();

    expect($member->paid_months)->toBe([1, 2, 3, 4, 5]);
    expect($member->is_up_to_date)->toBeTrue();
});

it('creates a Mercado Pago checkout for selected public fee months', function () {
    Carbon::setTestNow('2026-05-09 10:00:00');
    config(['services.mercado_pago.access_token' => 'test-token']);

    Http::fake([
        'api.mercadopago.com/checkout/preferences' => Http::response([
            'id' => 'preference-123',
            'init_point' => 'https://mercadopago.test/checkout/preference-123',
        ]),
    ]);

    $member = Member::create([
        'first_name' => 'Nina',
        'last_name' => 'Perez',
        'category' => 'infantiles',
        'document_number' => '33111222',
        'address' => 'Calle 40',
        'city' => 'Moron',
        'phone' => '444444',
        'responsible_adult_phone' => null,
        'paid_months' => [1, 2, 3, 4],
        'is_up_to_date' => false,
    ]);

    $response = post(route('fees.mercado-pago.store', $member), [
        'months' => [5],
    ]);

    $response->assertRedirect('https://mercadopago.test/checkout/preference-123');

    $feePayment = MemberFeePayment::first();

    expect($feePayment)->not->toBeNull();
    expect($feePayment->member_id)->toBe($member->id);
    expect($feePayment->months)->toBe([5]);
    expect((float) $feePayment->total_amount)->toBe(35000.0);
    expect($feePayment->status)->toBe('pending');
    expect($feePayment->provider_reference)->toBe('preference-123');

    Http::assertSent(function ($request) use ($feePayment) {
        return $request->url() === 'https://api.mercadopago.com/checkout/preferences'
            && $request['external_reference'] === (string) $feePayment->id
            && $request['notification_url'] === route('fees.mercado-pago.webhook')
            && $request['back_urls']['success'] === route('fees.mercado-pago.success', $feePayment);
    });
});

it('marks member fee months as paid when Mercado Pago webhook reports approval', function () {
    Carbon::setTestNow('2026-05-09 10:00:00');
    config(['services.mercado_pago.access_token' => 'test-token']);

    $member = Member::create([
        'first_name' => 'Valen',
        'last_name' => 'Lopez',
        'category' => 'juveniles',
        'document_number' => '34111222',
        'address' => 'Calle 50',
        'city' => 'Haedo',
        'phone' => '555555',
        'responsible_adult_phone' => null,
        'paid_months' => [1, 2, 3, 4],
        'is_up_to_date' => false,
    ]);

    $feePayment = MemberFeePayment::create([
        'member_id' => $member->id,
        'months' => [5],
        'base_amount' => 35000,
        'surcharge_amount' => 0,
        'total_amount' => 35000,
        'status' => 'pending',
        'payment_provider' => 'mercado_pago',
    ]);

    Http::fake([
        'api.mercadopago.com/v1/payments/123456' => Http::response([
            'id' => 123456,
            'status' => 'approved',
            'external_reference' => (string) $feePayment->id,
        ]),
    ]);

    $response = postJson(route('fees.mercado-pago.webhook'), [
        'data' => ['id' => 123456],
    ]);

    $response->assertOk();

    $member->refresh();
    $feePayment->refresh();

    expect($member->paid_months)->toBe([1, 2, 3, 4, 5]);
    expect($member->is_up_to_date)->toBeTrue();
    expect($feePayment->status)->toBe('paid');
    expect($feePayment->provider_payment_id)->toBe('123456');
    expect($feePayment->paid_at)->not->toBeNull();
});