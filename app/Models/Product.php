<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasSlug;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    use HasSlug;

    protected string $slugSource = 'name';

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

    /** @return HasMany<WishlistItem, $this> */
    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Scope a query to only include products in a specific collection.
     */
    #[Scope]
    protected function forCollection(Builder $query, int $collectionId): Builder
    {
        return $query->whereHas('collections', function (\Illuminate\Contracts\Database\Query\Builder $q) use ($collectionId) {
            $q->where('collections.id', $collectionId);
        });
    }

    /**
     * Scope a query to only include products in a specific category.
     */
    #[Scope]
    protected function forCategory(Builder $query, int $categoryId): Builder
    {
        return $query->whereHas('categories', function (\Illuminate\Contracts\Database\Query\Builder $q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }

    /**
     * Scope a query to filter products by attribute value slugs.
     */
    #[Scope]
    protected function withAttributeValues(Builder $query, array $valueSlugs): Builder
    {
        return $query->whereHas('attributeValues', function (\Illuminate\Contracts\Database\Query\Builder $q) use ($valueSlugs) {
            $q->whereIn('slug', $valueSlugs);
        });
    }
}
