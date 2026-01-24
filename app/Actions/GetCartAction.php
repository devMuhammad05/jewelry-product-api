<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\CartStatus;
use App\Models\Cart;
use Illuminate\Contracts\Cache\Repository;

final readonly class GetCartAction
{
    public function __construct(private Repository $cacheManager) {}

    /**
     * Execute the action to get the cart for a user or guest token.
     */
    public function execute(?int $userId, ?string $guestToken): ?Cart
    {
        if (! $userId && ! $guestToken) {
            return null;
        }

        $cacheKey = $userId
            ? "cart:user:{$userId}"
            : "cart:guest:{$guestToken}";

        return $this->cacheManager->remember($cacheKey, now()->addHours(24), function () use ($userId, $guestToken) {
            $query = Cart::query()
                ->with('items.variant.product')
                ->where('status', CartStatus::Active);

            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('guest_token', $guestToken);
            }

            return $query->first();
        });
    }
}
