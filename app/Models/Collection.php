<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Collection extends Model
{
    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Product, $this> */
    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'collection_products');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Collection, $this> */
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<Collection, $this> */
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
