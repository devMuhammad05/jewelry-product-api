<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait FacetedFiltering
{
    /**
     * Get facets for a given product query.
     */
    protected function getFacets(Builder $productQuery): Collection
    {
        // Get all attribute value IDs associated with the products in the query
        $attributeValueIds = $productQuery->clone()
            ->join('product_attribute_values', 'products.id', '=', 'product_attribute_values.product_id')
            ->select('product_attribute_values.attribute_value_id')
            ->distinct()
            ->pluck('attribute_value_id');

        // Fetch attributes and their values that are present in the results
        return Attribute::query()
            ->with(['values' => function ($query) use ($attributeValueIds) {
                $query->whereIn('id', $attributeValueIds)
                    ->withCount(['products' => function ($query) {
                        // This counts all products with this attribute value
                        // In a more advanced version, we would scope this further
                    }]);
            }])
            ->whereHas('values', function ($query) use ($attributeValueIds) {
                $query->whereIn('id', $attributeValueIds);
            })
            ->get();
    }
}
