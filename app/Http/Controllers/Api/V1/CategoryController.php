<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\AttributeResource;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\ProductResource;
use App\Http\Resources\Api\V1\CollectionResource;
use App\Models\Category;
use App\Models\Collection;
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
            ->allowedIncludes(['children', 'products', 'products.collections'])
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

        // Apply dynamic attribute-based filters
        $request = request();
        foreach ($request->query('filter', []) as $attributeSlug => $values) {
            // Convert comma-separated values to array
            $valueArray = is_array($values) ? $values : explode(',', $values);

            $productQuery->whereHas('attributeValues', function ($query) use ($valueArray) {
                $query->whereIn('slug', $valueArray);
            });
        }

        // Get facets AFTER applying filters (for dynamic facet counts)
        $facets = $this->getFacets($productQuery);

        // Apply includes and pagination
        $products = QueryBuilder::for($productQuery)
            ->allowedIncludes(['variants', 'attributeValues', 'images'])
            ->allowedSorts(['name', 'base_price', 'created_at'])
            ->paginate($request->input('per_page', 24));

        // Get relevant collections (collections that have products in this category)
        $collections = Collection::whereHas('products.categories', function ($query) use ($category) {
            $query->where('categories.id', $category->id);
        })->get();

        return $this->successResponse(
            'Category details retrieved successfully.',
            [
                'category' => new CategoryResource($category),
                'products' => ProductResource::collection($products),
                'collections' => CollectionResource::collection($collections),
                'facets' => AttributeResource::collection($facets),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
            ]
        );
    }
}
