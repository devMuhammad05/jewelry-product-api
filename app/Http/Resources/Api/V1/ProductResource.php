<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
final class ProductResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'variants' => $this->whenLoaded('variants', fn () => VariantResource::collection($this->variants)),
            'categories' => $this->whenLoaded('categories', fn () => CategoryResource::collection($this->categories)),
            'collections' => $this->whenLoaded('collections', fn () => CollectionResource::collection($this->collections)),
            'attribute_values' => $this->whenLoaded('attributeValues', fn () => AttributeValueResource::collection($this->attributeValues)),
        ];
    }
}
