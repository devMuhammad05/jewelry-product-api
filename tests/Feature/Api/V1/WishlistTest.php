<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\User;
use App\Models\Variant;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('guest can get their empty wishlist', function () {
    $token = (string) Str::uuid();

    // Create wishlist first to ensure it exists for the test
    Wishlist::factory()->create([
        'guest_token' => $token,
        'user_id' => null,
    ]);

    $response = $this->getJson("/api/v1/wishlist?guest_token={$token}");

    $response->assertSuccessful();
    $response->assertJsonPath('data.guest_token', $token);
    $response->assertJsonCount(0, 'data.items');
});

test('guest can add product to wishlist', function () {
    $variant = Variant::factory()->create();

    $response = $this->postJson('/api/v1/wishlist/items', [
        'variant_id' => $variant->id,
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'status',
        'message',
        'data' => [
            'id',
            'guest_token',
            'name',
            'items',
        ],
    ]);

    $this->assertDatabaseHas('wishlist_items', [
        'variant_id' => $variant->id,
    ]);
});

test('guest can remove item from wishlist', function () {
    $variant = Variant::factory()->create();
    $token = (string) Str::uuid();
    $wishlist = Wishlist::factory()->create([
        'guest_token' => $token,
        'user_id' => null,
    ]);
    $wishlist->items()->create(['variant_id' => $variant->id]);

    $response = $this->deleteJson("/api/v1/wishlist/items/{$variant->id}?guest_token={$token}");

    $response->assertSuccessful();
    $this->assertDatabaseMissing('wishlist_items', [
        'wishlist_id' => $wishlist->id,
        'variant_id' => $variant->id,
    ]);
});

test('authenticated user can get wishlist', function () {
    $user = User::factory()->create();
    Wishlist::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->getJson('/api/v1/wishlist');

    $response->assertSuccessful();
    $response->assertJsonPath('data.id', fn($id) => !is_null($id));
});

test('authenticated user can add item to wishlist', function () {
    $user = User::factory()->create();
    $variant = Variant::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/wishlist/items', [
            'variant_id' => $variant->id,
        ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('wishlist_items', [
        'variant_id' => $variant->id,
    ]);

    $this->assertDatabaseHas('wishlists', [
        'user_id' => $user->id,
    ]);
});

test('cannot add non-existent variant', function () {
    $response = $this->postJson('/api/v1/wishlist/items', [
        'variant_id' => 999999,
    ]);

    $response->assertStatus(422);
});
