<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class AttributeValue extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeValueFactory> */
    use HasFactory;

    /** @return BelongsTo<Attribute, $this> */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /** @return BelongsToMany<Product, $this> */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values');
    }
}
