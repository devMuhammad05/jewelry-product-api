<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasSlug;
use Database\Factories\AttributeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Attribute extends Model
{
    /** @use HasFactory<AttributeFactory> */
    use HasFactory;

    use HasSlug;

    protected string $slugSource = 'name';

    /** @return HasMany<AttributeValue, $this> */
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
}
