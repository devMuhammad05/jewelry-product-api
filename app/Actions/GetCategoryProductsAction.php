<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\QueryFilters\AttributeValuesFilter;
use App\Traits\FacetedFiltering;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class GetCategoryProductsAction
{
    use FacetedFiltering;

    /**
     * Execute the action to get filtered and paginated products for a category.
     */
    public function execute(Category $category, int $perPage = 24): array
    {
        // Build the base query using the scope
        $productQuery = Product::query()->forCategory($category->id);

        // Apply attribute filters using Spatie Query Builder
        $filteredQuery = QueryBuilder::for($productQuery)
            ->allowedFilters([
                AllowedFilter::custom('attributes', new AttributeValuesFilter),
            ])
            ->allowedIncludes(['variants', 'attributeValues', 'images'])
            ->allowedSorts(['name', 'base_price', 'created_at']);

        // Get the underlying Eloquent builder for facets
        $eloquentBuilder = $filteredQuery->getEloquentBuilder();

        // Get facets AFTER applying filters (for dynamic facet counts)
        $facets = $this->getFacets($eloquentBuilder);

        // Paginate the results
        $products = $filteredQuery->paginate($perPage);

        // Get relevant collections for this category
        $collections = Collection::whereHas('products.categories', function ($query) use ($category) {
            $query->where('categories.id', $category->id);
        })->get();

        return [
            'products' => $products,
            'facets' => $facets,
            'collections' => $collections,
        ];
    }
}
