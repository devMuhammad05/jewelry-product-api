<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\CartStatus;
use App\Models\Cart;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

final readonly class RemoveItemFromCartAction
{
    public function __construct(private Repository $cacheManager) {}

    /**
     * Execute the action to remove a product variant from the cart.
     */
    public function execute(?int $userId, ?string $guestToken, int $variantId): void
    {
        $cart = null;

        if ($userId) {
            $cart = Cart::where('user_id', $userId)
                ->where('status', CartStatus::Active)
                ->first();
        } elseif ($guestToken) {
            $cart = Cart::where('guest_token', $guestToken)
                ->where('status', CartStatus::Active)
                ->first();
        }

        if (! $cart) {
            return;
        }

        // 2. Remove the item
        $cart->items()->where('variant_id', $variantId)->delete();

        // 3. Invalidate cache
        $cacheKey = $userId ? "cart:user:{$userId}" : "cart:guest:{$cart->guest_token}";
        $this->cacheManager->forget($cacheKey);
    }
}
