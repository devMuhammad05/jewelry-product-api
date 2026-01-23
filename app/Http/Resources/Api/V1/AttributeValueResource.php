<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AttributeValue */
final class AttributeValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'slug' => $this->slug,
            'hex_color' => $this->hex_color,
            'product_count' => $this->when(isset($this->resource->products_count), $this->resource->products_count),
        ];
    }
}
