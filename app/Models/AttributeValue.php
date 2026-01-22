<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class AttributeValue extends Model
{
    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Attribute, $this> */
    public function attribute(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Product, $this> */
    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values');
    }
}
