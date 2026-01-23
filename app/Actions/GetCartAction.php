<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\CartStatus;
use App\Models\Cart;
use Illuminate\Support\Facades\Cache;

final class GetCartAction
{
    /**
     * Execute the action to get the cart for a user or guest token.
     */
    public function execute(?int $userId, ?string $guestToken): ?Cart
    {
        if (!$userId && !$guestToken) {
            return null;
        }

        $cacheKey = $userId
            ? "cart:user:{$userId}"
            : "cart:guest:{$guestToken}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($userId, $guestToken) {
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
