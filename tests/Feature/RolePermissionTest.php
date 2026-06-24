<?php

use App\Models\Role;
use App\Models\User;
use App\Support\UserRole;

it('allows admins to access protected administration routes', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    $this->actingAs($admin)
        ->get(route('products.index'))
        ->assertOk();
});

it('blocks socios from the internal dashboard and administration routes', function () {
    $socio = User::factory()->create(['role' => UserRole::SOCIO]);

    $this->actingAs($socio)
        ->get(route('dashboard'))
        ->assertRedirect(route('index', absolute: false));

    $this->actingAs($socio)
        ->get(route('products.index'))
        ->assertForbidden();
});

it('allows treasurers to manage store and fees without content permissions', function () {
    $treasurer = User::factory()->create(['role' => UserRole::TESORERO]);

    $this->actingAs($treasurer)
        ->get(route('products.index'))
        ->assertOk();

    $this->actingAs($treasurer)
        ->get(route('members.fee-payments.index'))
        ->assertOk();

    $this->actingAs($treasurer)
        ->get(route('publications.index'))
        ->assertForbidden();
});

it('keeps built-in role permissions when the role has no synced database permissions', function () {
    $role = Role::where('slug', UserRole::COORDINADOR)->firstOrFail();
    $role->permissions()->detach();

    $coordinator = User::factory()->create([
        'role' => UserRole::SOCIO,
        'role_id' => $role->id,
    ]);

    expect($coordinator->hasPermission('access-dashboard'))->toBeTrue();

    $this->actingAs($coordinator)
        ->get(route('dashboard'))
        ->assertOk();
});


it('creates public registrations as socios without administration permissions', function () {
    $this->post(route('register'), [
        'name' => 'Nuevo Socio',
        'email' => 'nuevo-socio@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('index', absolute: false));

    $this->assertDatabaseHas('users', [
        'email' => 'nuevo-socio@example.com',
        'role' => UserRole::SOCIO,
    ]);
});