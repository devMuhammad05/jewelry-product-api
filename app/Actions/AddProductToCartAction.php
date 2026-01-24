<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Models\Variant;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class AddProductToCartAction
{
    public function __construct(private Repository $cacheManager) {}

    /**
     * Execute the action to add a product variant to the cart.
     */
    public function execute(
        ?int $userId,
        ?string $guestToken,
        int $variantId,
        int $quantity
    ): array {

        $cart = $this->getOrCreateCart($userId, $guestToken);

        // 1. Validate variant existence and stock
        $variant = Variant::findOrFail($variantId);
        throw_if($variant->quantity < $quantity, InvalidArgumentException::class, 'Requested quantity exceeds available stock.');

        // 2. Add or update the cart item quantity
        $cartItem = $cart->items()->where('variant_id', $variantId)->first();
        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            throw_if($variant->quantity < $newQuantity, InvalidArgumentException::class, 'Total quantity exceeds available stock.');

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cartItem = $cart->items()->create([
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }

        // 3. Invalidate cache
        $cacheKey = $userId ? "cart:user:{$userId}" : "cart:guest:{$cart->guest_token}";
        $this->cacheManager->forget($cacheKey);

        return [
            'cart' => $cart->load('items.variant.product'),
            'guest_token' => $cart->guest_token,
        ];
    }

    private function getOrCreateCart(?int $userId, ?string $guestToken): Cart
    {
        if ($userId) {
            return Cart::firstOrCreate(
                ['user_id' => $userId, 'status' => CartStatus::Active],
                ['expires_at' => now()->addDays(30)]
            );
        }

        if ($guestToken && Str::isUuid($guestToken)) {
            $cart = Cart::where('guest_token', $guestToken)
                ->where('status', CartStatus::Active)
                ->first();

            if ($cart) {
                return $cart;
            }

            // If token provided but no active cart found, we REJECT it and generate a new one later.
        }

        // Generate guest_token(UUID) and create cart
        return Cart::create([
            'guest_token' => (string) Str::uuid(),
            'status' => CartStatus::Active,
            'expires_at' => now()->addHours(24),
        ]);
    }
}
