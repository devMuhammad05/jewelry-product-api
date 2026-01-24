<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Wishlist\AddProductToWishlistAction;
use App\Actions\Wishlist\GetWishlistAction;
use App\Actions\Wishlist\RemoveItemFromWishlistAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\WishlistResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

final class WishlistController extends ApiController
{
    public function index(Request $request, GetWishlistAction $action): JsonResponse
    {
        $wishlist = $action->execute(
            userId: (int) $request->user()?->id ?: null,
            guestToken: $request->query('guest_token')
        );

        if (! $wishlist) {
            return $this->successResponse('Wishlist is empty.', null);
        }

        return $this->successResponse(
            'Wishlist retrieved successfully.',
            new WishlistResource($wishlist)
        );
    }

    public function store(Request $request, AddProductToWishlistAction $action): JsonResponse
    {
        $request->validate([
            'variant_id' => ['required', 'integer', 'exists:variants,id'],
            'guest_token' => ['nullable', 'string', 'uuid'],
        ]);

        try {
            $result = $action->execute(
                userId: (int) $request->user()?->id ?: null,
                guestToken: $request->input('guest_token'),
                variantId: (int) $request->input('variant_id')
            );

            return $this->successResponse(
                'Product added to wishlist successfully.',
                [
                    'wishlist' => new WishlistResource($result['wishlist']),
                    'guest_token' => $result['guest_token'],
                ]
            );
        } catch (InvalidArgumentException | ModelNotFoundException $e) {
            return $this->validationErrorResponse($e->getMessage());
        }
    }

    public function destroy(Request $request, int $variantId, RemoveItemFromWishlistAction $action): JsonResponse
    {
        $action->execute(
            userId: (int) $request->user()?->id ?: null,
            guestToken: $request->input('guest_token'),
            variantId: $variantId
        );

        return $this->successResponse('Item removed from wishlist successfully.');
    }
}
