<?php

use App\Models\Member;
use App\Models\User;

it('filters members by full name tokens in search', function () {
    $user = User::factory()->create();

    Member::create([
        'first_name' => 'Maria',
        'last_name' => 'Gonzalez',
        'category' => 'Activa',
        'document_number' => '12345',
        'address' => 'Calle 1',
        'city' => 'Moron',
        'phone' => '111111',
        'responsible_adult_phone' => null,
        'paid_months' => [1, 2],
        'is_up_to_date' => false,
    ]);

    Member::create([
        'first_name' => 'Ana',
        'last_name' => 'Lopez',
        'category' => 'Infantil',
        'document_number' => '67890',
        'address' => 'Calle 2',
        'city' => 'Haedo',
        'phone' => '222222',
        'responsible_adult_phone' => null,
        'paid_months' => [1, 2, 3],
        'is_up_to_date' => true,
    ]);

    $response = actingAs($user)->get(route('members.index', ['search' => 'Maria Gonzalez']));

    $response->assertOk();
    $response->assertSee('Maria Gonzalez');
    $response->assertDontSee('Ana Lopez');
});

it('filters members by member id', function () {
    $user = User::factory()->create();

    $member = Member::create([
        'first_name' => 'Laura',
        'last_name' => 'Perez',
        'category' => 'Activa',
        'document_number' => '33333',
        'address' => 'Calle 3',
        'city' => 'Castelar',
        'phone' => '333333',
        'responsible_adult_phone' => null,
        'paid_months' => [1, 2, 3],
        'is_up_to_date' => true,
    ]);

    Member::create([
        'first_name' => 'Julia',
        'last_name' => 'Diaz',
        'category' => 'Cadete',
        'document_number' => '44444',
        'address' => 'Calle 4',
        'city' => 'Ituzaingo',
        'phone' => '444444',
        'responsible_adult_phone' => null,
        'paid_months' => [1],
        'is_up_to_date' => false,
    ]);

    $response = actingAs($user)->get(route('members.index', ['search' => (string) $member->id]));

    $response->assertOk();
    $response->assertSee('Laura Perez');
    $response->assertDontSee('Julia Diaz');
});