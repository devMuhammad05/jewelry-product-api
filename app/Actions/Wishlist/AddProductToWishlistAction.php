<?php

declare(strict_types=1);

namespace App\Actions\Wishlist;

use App\Models\Variant;
use App\Models\Wishlist;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class AddProductToWishlistAction
{
    public function __construct(private Repository $cacheManager) {}

    /**
     * Execute the action to add a product variant to the wishlist.
     */
    public function execute(
        ?int $userId,
        ?string $guestToken,
        int $variantId
    ): array {
        $wishlist = $this->getOrCreateWishlist($userId, $guestToken);

        // 1. Validate variant existence
        Variant::findOrFail($variantId);

        // 2. Add item if not exists (idempotency by unique constraint or check)
        // We use firstOrCreate to ensure we don't duplicate. 
        // Although DB has unique constraint, this prevents exception.
        $wishlist->items()->firstOrCreate([
            'variant_id' => $variantId,
        ]);

        // 3. Invalidate cache
        $cacheKey = $userId ? "wishlist:user:{$userId}" : "wishlist:guest:{$wishlist->guest_token}";
        $this->cacheManager->forget($cacheKey);

        return [
            'wishlist' => $wishlist->load('items.variant.product'),
            'guest_token' => $wishlist->guest_token,
        ];
    }

    private function getOrCreateWishlist(?int $userId, ?string $guestToken): Wishlist
    {
        if ($userId) {
            return Wishlist::firstOrCreate(
                ['user_id' => $userId],
                ['expires_at' => now()->addDays(30), 'name' => 'My Wishlist']
            );
        }

        if ($guestToken && Str::isUuid($guestToken)) {
            $wishlist = Wishlist::where('guest_token', $guestToken)->first();

            if ($wishlist) {
                return $wishlist;
            }
        }

        // Generate guest_token(UUID) and create wishlist
        return Wishlist::create([
            'guest_token' => (string) Str::uuid(),
            'name' => 'My Wishlist',
            'expires_at' => now()->addDays(30),
        ]);
    }
}
