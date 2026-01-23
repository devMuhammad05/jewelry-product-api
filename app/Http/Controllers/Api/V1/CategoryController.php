<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\GetCategoryProductsAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\AttributeResource;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\CollectionResource;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\QueryBuilder;

final class CategoryController extends ApiController
{
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
    public function show(string $slug, GetCategoryProductsAction $action): JsonResponse
    {
        $category = QueryBuilder::for(Category::class)
            ->where('slug', $slug)
            ->allowedIncludes(['children'])
            ->first();

        if (! $category) {
            return $this->errorResponse('Category not found.', 404);
        }

        $result = $action->execute($category, request()->input('per_page', 24));

        return $this->successResponse(
            'Category details retrieved successfully.',
            [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'image_url' => $category->image_url,
                'position' => $category->position,
                'children' => $category->relationLoaded('children') ? CategoryResource::collection($category->children) : null,
                'products' => ProductResource::collection($result['products']),
                'collections' => CollectionResource::collection($result['collections']),
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
