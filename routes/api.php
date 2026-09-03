<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // -------------------------------------------------------------
    // 1. Authentication Routes (Public)
    // -------------------------------------------------------------
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    // -------------------------------------------------------------
    // 2. Categories & Books (Public)
    // -------------------------------------------------------------
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/featured', [BookController::class, 'featured']);
    Route::get('/books/{id}', [BookController::class, 'show']);

    // -------------------------------------------------------------
    // 3. General & Public Services (Branches, Contact, Newsletter)
    // -------------------------------------------------------------
    Route::get('/branches', [GeneralController::class, 'branches']);
    Route::get('/features', [GeneralController::class, 'features']);
    Route::post('/newsletter/subscribe', [GeneralController::class, 'subscribeNewsletter']);
    Route::post('/contact-us', [GeneralController::class, 'sendContactMessage']);
    Route::get('/orders/track/{order_number}', [OrderController::class, 'trackOrder']);

    // Public / Guest checkout support (can be called with or without auth token)
    Route::post('/orders/checkout', [OrderController::class, 'checkout']);

    // -------------------------------------------------------------
    // 4. Protected Routes (Requires Bearer Token)
    // -------------------------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {

        // User Profile
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/change-password', [ProfileController::class, 'changePassword']);

        // Favorites / Wishlist
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);

        // Shopping Cart
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart/items', [CartController::class, 'addItem']);
        Route::delete('/cart/items/{id}', [CartController::class, 'removeItem']);
        Route::delete('/cart', [CartController::class, 'clearCart']);

        // User Orders History
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
    });
});



// => deprecated
// return response()
//     ->json($data)
//     ->header('Deprecation', 'true')
//     ->header(
//         'Sunset',
//         'Wed, 31 Dec 2026 23:59:59 GMT'
//     );
