<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\GetCollectionProductsAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\AttributeResource;
use App\Http\Resources\Api\V1\CollectionResource;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Collection;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\QueryBuilder;

final class CollectionController extends ApiController
{
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
    public function show(string $slug, GetCollectionProductsAction $action): JsonResponse
    {
        $collection = Collection::where('slug', $slug)->first();

        if (! $collection) {
            return $this->errorResponse('Collection not found.', 404);
        }

        $result = $action->execute($collection, request()->input('per_page', 24));

        return $this->successResponse(
            'Collection details retrieved successfully.',
            [
                'collection' => new CollectionResource($collection),
                'products' => ProductResource::collection($result['products']),
                'facets' => AttributeResource::collection($result['facets']),
                'meta' => [
                    'current_page' => $result['products']->currentPage(),
                    'last_page' => $result['products']->lastPage(),
                    'per_page' => $result['products']->perPage(),
                    'total' => $result['products']->total(),
                ],
            ]
        );
    }
}
