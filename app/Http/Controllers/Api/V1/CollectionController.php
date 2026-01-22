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

        // Get facets for this collection
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
            'Collection details retrieved successfully.',
            [
                'collection' => new CollectionResource($collection),
                'products' => ProductResource::collection($products),
                'facets' => AttributeResource::collection($facets),
            ]
        );
    }
}
