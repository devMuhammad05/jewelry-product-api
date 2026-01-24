<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\AddProductToCartAction;
use App\Actions\GetCartAction;
use App\Actions\RemoveItemFromCartAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\CartItemRequest;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

final class CartController extends ApiController
{
    public function index(Request $request, GetCartAction $action): JsonResponse
    {
        $cart = $action->execute(
            userId: (int) $request->user()?->id ?: null,
            guestToken: $request->query('guest_token')
        );

        return $this->successResponse('Cart retrieved successfully.', $cart);
    }

    public function store(CartItemRequest $request, AddProductToCartAction $action): JsonResponse
    {
        try {
            $result = $action->execute(
                userId: (int) $request->user()?->id ?: null,
                guestToken: $request->input('guest_token'),
                variantId: (int) $request->input('variant_id'),
                quantity: (int) $request->input('quantity')
            );

            return $this->successResponse(
                'Product added to cart successfully.',
                [
                    'cart' => $result['cart'],
                    'guest_token' => $result['guest_token'],
                ]
            );
        } catch (InvalidArgumentException $invalidArgumentException) {
            return $this->validationErrorResponse($invalidArgumentException->getMessage());
        }
    }

    public function destroy(Request $request, int $variantId, RemoveItemFromCartAction $action): JsonResponse
    {
        $action->execute(
            userId: (int) $request->user()?->id ?: null,
            guestToken: $request->input('guest_token'),
            variantId: $variantId
        );

        return $this->successResponse('Item removed from cart successfully.');
    }
}
