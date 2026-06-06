<?php

use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('lists active staff members on the public home page', function () {
    Staff::create([
        'name' => 'Carlos Lopez',
        'role' => 'Director técnico',
        'category' => 'EDEFI',
        'bio' => 'Formador de divisiones infantiles.',
        'display_order' => 1,
        'is_active' => true,
    ]);

    Staff::create([
        'name' => 'Integrante Inactivo',
        'role' => 'Preparador físico',
        'display_order' => 2,
        'is_active' => false,
    ]);

    $response = $this->get(route('index'));

    $response->assertOk();
    $response->assertSee('Carlos Lopez');
    $response->assertSee('Director técnico');
    $response->assertDontSee('Integrante Inactivo');
});

it('allows authenticated users to create staff members', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $response = actingAs($user)->post(route('staff.store'), [
        'name' => 'Mariana Perez',
        'role' => 'Coordinadora general',
        'category' => 'Coordinación',
        'bio' => 'Acompaña la planificación deportiva semanal.',
        'email' => 'mariana@example.com',
        'phone' => '1122334455',
        'display_order' => 3,
        'is_active' => '1',
        'photo' => UploadedFile::fake()->image('staff.jpg'),
    ]);

    $response->assertRedirect(route('staff.index'));

    $staff = Staff::query()->where('email', 'mariana@example.com')->first();

    expect($staff)->not->toBeNull()
        ->and($staff->name)->toBe('Mariana Perez')
        ->and($staff->is_active)->toBeTrue();

    Storage::disk('public')->assertExists($staff->photo_path);
});