<?php

test('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('shows training schedules by category and venue', function () {
    $response = $this->get('/');

    $response
        ->assertOk()
        ->assertSee('Horarios por categoría y sede')
        ->assertSeeInOrder(['Edefi', 'Morón', 'Lunes y miércoles', '18:00'])
        ->assertSeeInOrder(['Futsal femenino', 'Castelar', 'Martes y jueves', '19:00']);
});