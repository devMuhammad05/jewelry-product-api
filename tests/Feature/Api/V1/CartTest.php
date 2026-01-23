<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

use App\Models\Cart;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Support\Str;

test('guest can add product to cart and receive a guest token', function () {
    $variant = Variant::factory()->create(['quantity' => 10]);

    $response = $this->postJson('/api/v1/cart/items', [
        'variant_id' => $variant->id,
        'quantity' => 2,
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'message',
        'data' => [
            'id',
            'guest_token',
            'items',
        ],
        'guest_token',
    ]);

    $this->assertDatabaseHas('carts', [
        'guest_token' => $response->json('guest_token'),
    ]);

    $this->assertDatabaseHas('cart_items', [
        'variant_id' => $variant->id,
        'quantity' => 2,
    ]);
});

test('guest can add product using an existing valid guest token', function () {
    $cart = Cart::factory()->create(['guest_token' => (string) Str::uuid()]);
    $variant = Variant::factory()->create(['quantity' => 10]);

    $response = $this->postJson('/api/v1/cart/items', [
        'variant_id' => $variant->id,
        'quantity' => 1,
        'guest_token' => $cart->guest_token,
    ]);

    $response->assertSuccessful();
    expect($response->json('guest_token'))->toBe($cart->guest_token);
});

test('guest request with non-existent token generates a new one', function () {
    $variant = Variant::factory()->create(['quantity' => 10]);
    $fakeToken = (string) Str::uuid();

    $response = $this->postJson('/api/v1/cart/items', [
        'variant_id' => $variant->id,
        'quantity' => 1,
        'guest_token' => $fakeToken,
    ]);

    $response->assertSuccessful();
    expect($response->json('guest_token'))->not->toBe($fakeToken);
});

test('authenticated user can add product to cart', function () {
    $user = User::factory()->create();
    $variant = Variant::factory()->create(['quantity' => 10]);

    $response = $this->actingAs($user)
        ->postJson('/api/v1/cart/items', [
            'variant_id' => $variant->id,
            'quantity' => 1,
        ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('carts', [
        'user_id' => $user->id,
    ]);
});

test('adding same product increments its quantity', function () {
    $variant = Variant::factory()->create(['quantity' => 10]);
    $token = (string) Str::uuid();
    $cart = Cart::factory()->create(['guest_token' => $token]);
    $cart->items()->create(['variant_id' => $variant->id, 'quantity' => 2]);

    $response = $this->postJson('/api/v1/cart/items', [
        'variant_id' => $variant->id,
        'quantity' => 3,
        'guest_token' => $token,
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('cart_items', [
        'variant_id' => $variant->id,
        'quantity' => 5,
    ]);
});

test('cannot add product if quantity exceeds stock', function () {
    $variant = Variant::factory()->create(['quantity' => 5]);

    $response = $this->postJson('/api/v1/cart/items', [
        'variant_id' => $variant->id,
        'quantity' => 10,
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('message', 'Requested quantity exceeds available stock.');
});

test('total quantity in cart cannot exceed stock', function () {
    $variant = Variant::factory()->create(['quantity' => 5]);
    $token = (string) Str::uuid();
    $cart = Cart::factory()->create(['guest_token' => $token]);
    $cart->items()->create(['variant_id' => $variant->id, 'quantity' => 3]);

    $response = $this->postJson('/api/v1/cart/items', [
        'variant_id' => $variant->id,
        'quantity' => 3,
        'guest_token' => $token,
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('message', 'Total quantity exceeds available stock.');
});

test('guest can fetch their cart', function () {
    $variant = Variant::factory()->create();
    $token = (string) Str::uuid();
    $cart = Cart::factory()->create(['guest_token' => $token]);
    $cart->items()->create(['variant_id' => $variant->id, 'quantity' => 2]);

    $response = $this->getJson("/api/v1/cart?guest_token={$token}");

    $response->assertSuccessful();
    $response->assertJsonPath('data.guest_token', $token);
    $response->assertJsonCount(1, 'data.items');
    $response->assertJsonPath('data.items.0.variant_id', $variant->id);
});

test('authenticated user can fetch their cart', function () {
    $user = User::factory()->create();
    $variant = Variant::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $cart->items()->create(['variant_id' => $variant->id, 'quantity' => 1]);

    $response = $this->actingAs($user)
        ->getJson('/api/v1/cart');

    $response->assertSuccessful();
    $response->assertJsonPath('data.user_id', $user->id);
    $response->assertJsonCount(1, 'data.items');
});

test('cart endpoint returns null when no cart exists', function () {
    $response = $this->getJson('/api/v1/cart'); // No token, no auth

    $response->assertSuccessful();
    $response->assertJsonPath('data', null);
});
