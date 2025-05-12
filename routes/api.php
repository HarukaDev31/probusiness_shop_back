<?php

use Illuminate\Http\Request;
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