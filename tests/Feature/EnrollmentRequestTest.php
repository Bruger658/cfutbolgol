<?php

use App\Mail\EnrollmentRequestReceived;
use App\Models\EnrollmentRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

it('stores public enrollment requests and notifies the club', function () {
    Mail::fake();
    config(['services.enrollment.club_email' => 'club@example.com']);

    $this->post(route('enrollment-requests.store'), [
        'player_name' => 'Juan Pérez',
        'birth_date' => '2014-05-20',
        'guardian_email' => 'familia@example.com',
        'contact_phone' => '+54 11 5555 1234',
        'category' => 'Edefi',
    ])
        ->assertRedirect(route('index').'#inscripcion')
        ->assertSessionHas('enrollment_status');

    $this->assertDatabaseHas('enrollment_requests', [
        'player_name' => 'Juan Pérez',
        'guardian_email' => 'familia@example.com',
        'contact_phone' => '+54 11 5555 1234',
        'category' => 'Edefi',
        'status' => EnrollmentRequest::STATUS_PENDING,
    ]);

    Mail::assertSent(EnrollmentRequestReceived::class, fn ($mail) => $mail->hasTo('club@example.com'));
});

it('lets admins list and update enrollment statuses', function () {
    $user = User::factory()->create();
    $enrollmentRequest = EnrollmentRequest::create([
        'player_name' => 'Lucía Gómez',
        'birth_date' => '2013-08-10',
        'guardian_email' => 'familia@example.com',
        'contact_phone' => '+54 11 5555 6789',
        'category' => 'Futsal Femenino',
        'status' => EnrollmentRequest::STATUS_PENDING,
    ]);

    $this->actingAs($user)
        ->get(route('enrollment-requests.index'))
        ->assertOk()
        ->assertSee('Lucía Gómez')
        ->assertSee('Futsal Femenino');

    $this->actingAs($user)
        ->put(route('enrollment-requests.update', $enrollmentRequest), [
            'status' => EnrollmentRequest::STATUS_TRIAL_SCHEDULED,
        ])
        ->assertRedirect(route('enrollment-requests.index'))
        ->assertSessionHas('status');

    $enrollmentRequest->refresh();

    expect($enrollmentRequest->status)->toBe(EnrollmentRequest::STATUS_TRIAL_SCHEDULED)
        ->and($enrollmentRequest->contacted_at)->not->toBeNull()
        ->and($enrollmentRequest->trial_scheduled_at)->not->toBeNull();
});