<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Wishlist $this */
        return [
            'id' => $this->id,
            'guest_token' => $this->when($this->user_id === null, $this->guest_token),
            'name' => $this->name,
            'items' => WishlistItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
