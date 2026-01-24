<?php

declare(strict_types=1);

namespace App\Actions\Wishlist;

use App\Models\Wishlist;
use Illuminate\Contracts\Cache\Repository;

final readonly class RemoveItemFromWishlistAction
{
    public function __construct(private Repository $cacheManager) {}

    /**
     * Execute the action to remove an item from the wishlist.
     */
    public function execute(
        ?int $userId,
        ?string $guestToken,
        int $variantId
    ): void {
        if (! $userId && ! $guestToken) {
            return;
        }

        $query = Wishlist::query();

        if ($userId) {
            $query->where('user_id', $userId);
            $cacheKey = "wishlist:user:{$userId}";
        } else {
            $query->where('guest_token', $guestToken);
            $cacheKey = "wishlist:guest:{$guestToken}";
        }

        /** @var Wishlist|null $wishlist */
        $wishlist = $query->first();

        if ($wishlist) {
            $wishlist->items()->where('variant_id', $variantId)->delete();
            $this->cacheManager->forget($cacheKey);
        }
    }
}
