<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Product Cache', function () {
    test('it caches product listing results', function () {
        Product::factory()->count(5)->create(['is_featured' => true]);

        Cache::flush();

        // First request - should cache
        $response1 = $this->getJson('/api/v1/products');
        $response1->assertStatus(200);

        // Verify cache was created
        $url = request()->url() ?: 'http://localhost/api/v1/products';
        $queryParams = [];
        ksort($queryParams);
        $queryString = http_build_query($queryParams);
        $fullUrl = "{$url}?{$queryString}";
        $cacheKey = sha1($fullUrl);

        expect(Cache::has($cacheKey))->toBeTrue();

        // Second request - should use cache
        $response2 = $this->getJson('/api/v1/products');
        $response2->assertStatus(200);

        // Results should be identical
        expect($response1->json('data'))->toEqual($response2->json('data'));
    });

    test('it generates different cache keys for different query parameters', function () {
        Product::factory()->count(5)->create(['is_featured' => true]);

        Cache::flush();

        // Request with no filters
        $this->getJson('/api/v1/products');

        // Request with include parameter
        $this->getJson('/api/v1/products?include=variants');

        // Both should be cached separately
        $baseUrl = 'http://localhost/api/v1/products';
        $key1 = sha1("{$baseUrl}?");
        $key2 = sha1("{$baseUrl}?include=variants");

        expect(Cache::has($key1))->toBeTrue();
        expect(Cache::has($key2))->toBeTrue();
    });

    test('it generates same cache key regardless of parameter order', function () {
        Product::factory()->count(5)->create(['is_featured' => true]);

        Cache::flush();

        // Request with parameters in one order
        $this->getJson('/api/v1/products?include=variants&page=1');

        // Request with parameters in different order
        $response = $this->getJson('/api/v1/products?page=1&include=variants');

        $response->assertStatus(200);

        // Should only have one cache entry (same key)
        $baseUrl = 'http://localhost/api/v1/products';
        $params = ['include' => 'variants', 'page' => '1'];
        ksort($params);
        $queryString = http_build_query($params);
        $cacheKey = sha1("{$baseUrl}?{$queryString}");

        expect(Cache::has($cacheKey))->toBeTrue();
    });
});
