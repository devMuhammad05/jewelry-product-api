<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Attribute extends Model
{
    /** @use HasFactory<\Database\Factories\AttributeFactory> */
    use HasFactory;

    /** @return HasMany<AttributeValue, $this> */
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
}
