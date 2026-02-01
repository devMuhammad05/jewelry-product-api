<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\GetProductsAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\QueryBuilder;

final class ProductController extends ApiController
{
    /**
     * Display a listing of the products.
     */
    public function index(GetProductsAction $action): JsonResponse
    {
        $products = $action->execute();

        return $this->successResponse(
            'Products retrieved successfully.',
            ProductResource::collection($products)
        );
    }

    /**
     * Display the specified product by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $product = QueryBuilder::for(Product::class)
            ->where('slug', $slug)
            ->allowedIncludes(['variants', 'categories', 'collections', 'attributeValues.attribute'])
            ->first();

        if (! $product) {
            return $this->errorResponse('Product not found.', 404);
        }

        return $this->successResponse(
            'Product details retrieved successfully.',
            new ProductResource($product)
        );
    }
}
