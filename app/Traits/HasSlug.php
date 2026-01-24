<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            $source = $model->getSlugSourceColumn();

            if (empty($model->slug)) {
                $model->slug = Str::slug($model->getAttribute($source));
            }
        });

        static::updating(function (Model $model) {
            $source = $model->getSlugSourceColumn();

            if ($model->isDirty($source) && ! $model->isDirty('slug')) {
                $model->slug = Str::slug($model->getAttribute($source));
            }
        });
    }

    public function getSlugSourceColumn(): string
    {
        return property_exists($this, 'slugSource') ? $this->slugSource : 'name';
    }
}
