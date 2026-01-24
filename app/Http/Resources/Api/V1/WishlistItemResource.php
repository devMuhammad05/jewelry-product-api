<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class WishlistItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var WishlistItem $this */
        return [
            'id' => $this->id,
            'variant_id' => $this->variant_id,
            'variant' => new VariantResource($this->whenLoaded('variant')),
            'note' => $this->note,
            'added_at' => $this->created_at->toIso8601String(),
        ];
    }
}
