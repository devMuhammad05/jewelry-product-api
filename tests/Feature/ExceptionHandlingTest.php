<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

uses(TestCase::class);

it('returns a custom json response for method not allowed on api routes', function () {
    $response = $this->postJson('/api/v1/collections');

    $response->assertStatus(405)
        ->assertJson([
            'status' => 'error',
            'message' => 'The POST method is not allowed for this endpoint.',
        ]);
});

it('returns a custom json response for route not found on api routes', function () {
    $response = $this->postJson('/api/v1/non-existent-route');

    $response->assertNotFound()
        ->assertJson([
            'status' => 'error',
            'message' => 'Route not found.',
        ]);
});
