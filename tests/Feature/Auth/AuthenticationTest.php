<?php

use App\Models\User;
use App\Support\UserRole;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('authenticated users can render the login screen', function () {
    $user = User::factory()->create(['role' => UserRole::SOCIO]);

    $response = $this->actingAs($user)->get('/login');

    $response->assertStatus(200);
    $response->assertViewIs('auth.login');
});

test('backend url redirects guests to the login screen', function () {
    $response = $this->get('/backend');

    $response->assertRedirect(route('login', absolute: false));
});

test('backend url redirects authenticated users to the dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/backend');

    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users without dashboard permission are redirected home after login', function () {
    $user = User::factory()->create(['role' => UserRole::SOCIO]);

    $this->get('/dashboard')->assertRedirect('/login');

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('index', absolute: false));
});


test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});