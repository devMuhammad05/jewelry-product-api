<?php

declare(strict_types=1);

namespace App\Actions;

use App\Traits\CacheableQuery;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

final class GetProductsAction
{
    use CacheableQuery;

    /**
     * Execute the action to get products (featured or fallback to all) in random order.
     */
    public function execute(): LengthAwarePaginator
    {
        return $this->rememberQuery(function () {
            $query = QueryBuilder::for(Product::class)
                ->allowedIncludes(['variants', 'categories', 'collections', 'attributeValues.attribute'])
                ->allowedFilters(['status']);

            $hasFeatured = Product::query()->where('is_featured', true)->exists();

            if ($hasFeatured) {
                $query->where('is_featured', true);
            }

            return $query->inRandomOrder()->paginate();
        }, minutes: 60);
    }
}
