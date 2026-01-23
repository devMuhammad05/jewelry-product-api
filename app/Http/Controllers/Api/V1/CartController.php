<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\AddProductToCartAction;
use App\Actions\GetCartAction;
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

        return response()->json([
            'data' => $cart,
        ]);
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

            return response()->json([
                'message' => 'Product added to cart successfully.',
                'data' => $result['cart'],
                'guest_token' => $result['guest_token'],
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
