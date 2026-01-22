<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\AttributeResource;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Traits\FacetedFiltering;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class CategoryController extends ApiController
{
    use FacetedFiltering;

    /**
     * Display a listing of top-level categories.
     */
    public function index(): JsonResponse
    {
        $categories = QueryBuilder::for(Category::class)
            ->whereNull('parent_id')
            ->allowedIncludes(['children', 'products'])
            ->orderBy('position')
            ->get();

        return $this->successResponse(
            'Categories retrieved successfully.',
            CategoryResource::collection($categories)
        );
    }

    /**
     * Display the specified category by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->first();

        if (! $category) {
            return $this->errorResponse('Category not found.', 404);
        }

        $productQuery = Product::query()->whereHas('categories', function ($query) use ($category) {
            $query->where('categories.id', $category->id);
        });

        // Get facets for this category
        $facets = $this->getFacets($productQuery);

        // Apply filters and includes
        $products = QueryBuilder::for($productQuery)
            ->allowedIncludes(['variants'])
            ->allowedFilters([
                AllowedFilter::callback('metal', function ($query, $value) {
                    $query->whereHas('attributeValues', function ($q) use ($value) {
                        $q->where('slug', $value);
                    });
                }),
                AllowedFilter::callback('stone_shape', function ($query, $value) {
                    $query->whereHas('attributeValues', function ($q) use ($value) {
                        $q->where('slug', $value);
                    });
                }),
            ])
            ->get();

        return $this->successResponse(
            'Category details retrieved successfully.',
            [
                'category' => new CategoryResource($category),
                'products' => ProductResource::collection($products),
                'facets' => AttributeResource::collection($facets),
            ]
        );
    }
}
