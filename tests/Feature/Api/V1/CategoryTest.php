<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Category API', function () {
    test('it can list top-level categories', function () {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'position',
                    ]
                ],
            ]);
    });

    test('it can show a category with its children', function () {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $response = $this->getJson("/api/v1/categories/{$parent->slug}?include=children");

        $response->assertStatus(200)
            ->assertJsonPath('data.category.name', $parent->name)
            ->assertJsonCount(1, 'data.category.children');
    });

    test('it can show a category with its products', function () {
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $category->products()->attach($product);

        $response = $this->getJson("/api/v1/categories/{$category->slug}?include=products");

        $response->assertStatus(200)
            ->assertJsonPath('data.category.name', $category->name)
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.name', $product->name);
    });

    test('it can show a category with its relevant collections', function () {
        $category = Category::factory()->create();
        $collection = \App\Models\Collection::factory()->create();
        $product = Product::factory()->create();

        $category->products()->attach($product);
        $collection->products()->attach($product);

        $response = $this->getJson("/api/v1/categories/{$category->slug}");

        $response->assertStatus(200)
            ->assertJsonPath('data.category.name', $category->name)
            ->assertJsonCount(1, 'data.collections')
            ->assertJsonPath('data.collections.0.name', $collection->name);
    });

    test('it returns 404 for non-existent category', function () {

        $response = $this->getJson('/api/v1/categories/non-existent-category');

        $response->assertStatus(404);
    });
});
