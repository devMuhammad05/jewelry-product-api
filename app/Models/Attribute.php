<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Attribute extends Model
{
    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<AttributeValue, $this> */
    public function values(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
}
