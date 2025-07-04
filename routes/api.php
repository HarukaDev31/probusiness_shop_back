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
Route::middleware('throttle:5,1')->post('/orders', [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
//for post products
Route::middleware('throttle:20,1')->post('/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
Route::middleware('guest.api')->post('/auth/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('auth.register');
Route::middleware('guest.api')->post('/auth/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('auth.login');
Route::middleware('throttle:20,1')->get('/getProductsToScrapping', [\App\Http\Controllers\ScrappingController::class, 'getProductsToScrapping'])->name('scrapping.getProductsToScrapping');
Route::middleware('throttle:60,1')->post('/markProductsCompleted', [\App\Http\Controllers\ScrappingController::class, 'markProductsCompleted'])->name('scrapping.markProductsCompleted');
