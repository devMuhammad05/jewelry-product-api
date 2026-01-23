<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\AttributeResource;
use App\Http\Resources\Api\V1\CollectionResource;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Collection;
use App\Models\Product;
use App\Traits\FacetedFiltering;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class CollectionController extends ApiController
{
    use FacetedFiltering;

    /**
     * Display a listing of top-level collections.
     */
    public function index(): JsonResponse
    {
        $collections = QueryBuilder::for(Collection::class)
            ->whereNull('parent_id')
            ->allowedIncludes(['children', 'products'])
            ->orderBy('position')
            ->get();

        return $this->successResponse(
            'Collections retrieved successfully.',
            CollectionResource::collection($collections)
        );
    }

    /**
     * Display the specified collection by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $collection = Collection::where('slug', $slug)->first();

        if (! $collection) {
            return $this->errorResponse('Collection not found.', 404);
        }

        $productQuery = Product::query()->whereHas('collections', function ($query) use ($collection) {
            $query->where('collections.id', $collection->id);
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

        return $this->successResponse(
            'Collection details retrieved successfully.',
            [
                'collection' => new CollectionResource($collection),
                'products' => ProductResource::collection($products),
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
