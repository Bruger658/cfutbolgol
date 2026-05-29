<?php

use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    config([
        'membership.monthly_fee' => 10000,
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
    $response->assertSee('$10.000');
    $response->assertSee('$1.000');
    $response->assertSee('$11.000');
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