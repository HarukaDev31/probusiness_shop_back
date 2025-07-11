<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('throttle:100,1')->get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::middleware('throttle:100,1')->get('/categories', [\App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
Route::middleware('throttle:100,1')->get('/products/{id}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
Route::middleware(['validate.token', 'throttle:10,1'])->post('/orders', [\App\Http\Controllers\NewOrderController::class, 'store'])->name('orders.new.store');
Route::middleware(['validate.token', 'throttle:100,1'])->get('/orders/my-orders', [\App\Http\Controllers\NewOrderController::class, 'myOrders'])->name('orders.my-orders');
//for post products
Route::middleware('throttle:20,1')->post('/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
Route::middleware(['guest.api', 'throttle:5,1'])->post('/auth/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('auth.register');
Route::middleware(['guest.api', 'throttle:10,1'])->post('/auth/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('auth.login');

// User routes
Route::middleware(['validate.token', 'throttle:100,1'])->get('/me', [\App\Http\Controllers\UserController::class, 'me'])->name('user.me');
Route::middleware(['validate.token', 'throttle:20,1'])->put('/me', [\App\Http\Controllers\UserController::class, 'update'])->name('user.update');

Route::middleware('throttle:20,1')->get('/getProductsToScrapping', [\App\Http\Controllers\ScrappingController::class, 'getProductsToScrapping'])->name('scrapping.getProductsToScrapping');
Route::middleware('throttle:60,1')->post('/markProductsCompleted', [\App\Http\Controllers\ScrappingController::class, 'markProductsCompleted'])->name('scrapping.markProductsCompleted');

// Wishlist routes
Route::middleware(['auth.api', 'throttle:20,1'])->group(function () {
    Route::post('/wishlist', [\App\Http\Controllers\WishlistController::class, 'store'])->name('wishlist.store');
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::delete('/wishlist/{id}', [\App\Http\Controllers\WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

// Location routes
Route::middleware('throttle:100,1')->group(function () {
    Route::get('/location/paises', [\App\Http\Controllers\LocationController::class, 'getPaises'])->name('location.paises');
    Route::get('/location/departamentos', [\App\Http\Controllers\LocationController::class, 'getDepartamentos'])->name('location.departamentos');
    Route::post('/location/provincias', [\App\Http\Controllers\LocationController::class, 'getProvincias'])->name('location.provincias');
    Route::post('/location/distritos', [\App\Http\Controllers\LocationController::class, 'getDistritos'])->name('location.distritos');
});

// Order details route
Route::middleware(['validate.token', 'throttle:100,1'])->get('/orders/{orderId}/details', [\App\Http\Controllers\NewOrderController::class, 'getOrderDetails'])->name('orders.details');
