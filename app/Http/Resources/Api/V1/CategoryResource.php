<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Category */
final class CategoryResource extends JsonResource
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
            'image_url' => $this->image_url,
            'position' => $this->position,
            'parent_id' => $this->parent_id,
            'children' => $this->whenLoaded('children', fn() => CategoryResource::collection($this->children)),
            'products' => $this->whenLoaded('products', fn() => ProductResource::collection($this->products)),
            // 'collections' => $this->when($this->relationLoaded('products'), function () {
            //     return CollectionResource::collection(
            //         $this->products->flatMap->collections->unique('id')
            //     );
            // }),
            'products_count' => $this->whenCounted('products'),
        ];
    }
}
