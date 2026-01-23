<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;


    /** @return HasMany<Variant, $this> */
    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    /** @return BelongsToMany<Category, $this> */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }

    /** @return BelongsToMany<Collection, $this> */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_products');
    }

    /** @return BelongsToMany<AttributeValue, $this> */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values');
    }

    /**
     * Scope a query to only include products in a specific collection.
     */
    public function scopeForCollection(Builder $query, int $collectionId): Builder
    {
        return $query->whereHas('collections', function ($q) use ($collectionId) {
            $q->where('collections.id', $collectionId);
        });
    }

    /**
     * Scope a query to only include products in a specific category.
     */
    public function scopeForCategory(Builder $query, int $categoryId): Builder
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }

    /**
     * Scope a query to filter products by attribute value slugs.
     */
    public function scopeWithAttributeValues(Builder $query, array $valueSlugs): Builder
    {
        return $query->whereHas('attributeValues', function ($q) use ($valueSlugs) {
            $q->whereIn('slug', $valueSlugs);
        });
    }
}
