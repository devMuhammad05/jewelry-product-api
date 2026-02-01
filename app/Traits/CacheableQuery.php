<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheableQuery
{
    /**
     * Cache the results of a query based on the current URL and parameters.
     */
    public function rememberQuery(callable $callback, string $keySuffix = '', int $minutes = 60): mixed
    {
        $url = request()->url();
        $queryParams = request()->query();

        ksort($queryParams);

        $queryString = http_build_query($queryParams);
        $fullUrl = "{$url}?{$queryString}";

        $rememberKey = sha1($fullUrl.$keySuffix);

        return Cache::remember($rememberKey, now()->addMinutes($minutes), $callback);
    }
}
