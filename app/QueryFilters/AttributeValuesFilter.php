<?php

declare(strict_types=1);

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

final class AttributeValuesFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        // Convert comma-separated values to array
        $valueSlugs = is_array($value) ? $value : explode(',', $value);

        $query->withAttributeValues($valueSlugs);
    }
}
