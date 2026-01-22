<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Product extends Model
{
    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<Variant, $this> */
    public function variants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Variant::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Category, $this> */
    public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Collection, $this> */
    public function collections(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_products');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<AttributeValue, $this> */
    public function attributeValues(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values');
    }
}
