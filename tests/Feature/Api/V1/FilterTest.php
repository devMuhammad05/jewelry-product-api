<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Faceted Filtering API', function () {
    test('it can filter products by attribute slug', function () {
        $category = Category::factory()->create();

        $metal = Attribute::factory()->create(['name' => 'Metal', 'slug' => 'metal']);
        $platinum = AttributeValue::factory()->create(['attribute_id' => $metal->id, 'value' => 'Platinum', 'slug' => 'platinum']);
        $gold = AttributeValue::factory()->create(['attribute_id' => $metal->id, 'value' => 'Gold', 'slug' => 'gold']);

        $platinumProduct = Product::factory()->create();
        $platinumProduct->categories()->attach($category);
        $platinumProduct->attributeValues()->attach($platinum);

        $goldProduct = Product::factory()->create();
        $goldProduct->categories()->attach($category);
        $goldProduct->attributeValues()->attach($gold);

        // Filter by platinum
        $response = $this->getJson("/api/v1/categories/{$category->slug}?filter[metal]=platinum");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.name', $platinumProduct->name);
    });

    test('it returns available facets for the current category', function () {
        $category = Category::factory()->create();

        $metal = Attribute::factory()->create(['name' => 'Metal', 'slug' => 'metal']);
        $platinum = AttributeValue::factory()->create(['attribute_id' => $metal->id, 'value' => 'Platinum', 'slug' => 'platinum']);

        $product = Product::factory()->create();
        $product->categories()->attach($category);
        $product->attributeValues()->attach($platinum);

        $response = $this->getJson("/api/v1/categories/{$category->slug}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'facets' => [
                        '*' => [
                            'name',
                            'slug',
                            'values' => [
                                '*' => ['value', 'slug', 'product_count']
                            ]
                        ]
                    ]
                ]
            ])
            ->assertJsonPath('data.facets.0.name', 'Metal')
            ->assertJsonPath('data.facets.0.values.0.value', 'Platinum');
    });
});
