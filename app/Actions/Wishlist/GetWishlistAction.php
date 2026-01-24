<?php

declare(strict_types=1);

namespace App\Actions\Wishlist;

use App\Models\Wishlist;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Str;

final readonly class GetWishlistAction
{
    public function __construct(private Repository $cacheManager) {}

    /**
     * Execute the action to get the wishlist for a user or guest token.
     */
    public function execute(?int $userId, ?string $guestToken): ?Wishlist
    {
        if (! $userId && ! $guestToken) {
            return null;
        }

        $cacheKey = $userId
            ? "wishlist:user:{$userId}"
            : "wishlist:guest:{$guestToken}";

        return $this->cacheManager->remember($cacheKey, now()->addHours(24), function () use ($userId, $guestToken) {
            $query = Wishlist::query()
                ->with('items.variant.product');

            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('guest_token', $guestToken);
            }

            return $query->first();
        });
    }
}
