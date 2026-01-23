<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Collection;
use App\Models\Product;
use App\QueryFilters\AttributeValuesFilter;
use App\Traits\FacetedFiltering;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class GetCollectionProductsAction
{
    use FacetedFiltering;

    /**
     * Execute the action to get filtered and paginated products for a collection.
     */
    public function execute(Collection $collection, int $perPage = 24): array
    {
        // Build the base query using the scope
        $productQuery = Product::query()->forCollection($collection->id);

        // Apply attribute filters using Spatie Query Builder
        $filters = request()->query('filter', []);
        $allowedFilters = [];
        foreach (array_keys($filters) as $filterName) {
            $allowedFilters[] = AllowedFilter::custom((string) $filterName, new AttributeValuesFilter());
        }

        $filteredQuery = QueryBuilder::for($productQuery)
            ->allowedFilters($allowedFilters)
            ->allowedIncludes(['variants', 'attributeValues'])
            ->allowedSorts(['name', 'base_price', 'created_at']);

        // Get the underlying Eloquent builder for facets
        $eloquentBuilder = $filteredQuery->getEloquentBuilder();

        // Get facets AFTER applying filters (for dynamic facet counts)
        $facets = $this->getFacets($eloquentBuilder);
        $products = $filteredQuery->paginate($perPage);

        return [
            'products' => $products,
            'facets' => $facets,
        ];
    }
}
