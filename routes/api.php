<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CollectionController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/', fn () => 'API is active');

    // Auth Routes
    Route::prefix('auth')->group(function (): void {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);

    // Collections
    Route::get('/collections', [CollectionController::class, 'index']);
    Route::get('/collections/{slug}', [CollectionController::class, 'show']);

    // Products
    Route::get('/products/{slug}', [ProductController::class, 'show']);

    // Cart
    Route::prefix('cart')->group(function (): void {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/items', [CartController::class, 'store']);
        Route::delete('/items/{variant_id}', [CartController::class, 'destroy']);
    });

    // Wishlist
    Route::prefix('wishlist')->group(function (): void {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('/items', [WishlistController::class, 'store']);
        Route::delete('/items/{variant_id}', [WishlistController::class, 'destroy']);
    });
});
