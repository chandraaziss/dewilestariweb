<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminAuthController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/api/products', [ProductController::class, 'api']);
Route::middleware('admin')->group(function() {
Route::get('/admin/products', [ProductController::class, 'index']);
Route::get('/admin/orders',[OrderController::class,'index']);
Route::get('/admin/reports',[OrderController::class, 'report']);

        });
Route::get('/admin/login', [AdminAuthController::class, 'loginForm']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::get('/admin/logout', [AdminAuthController::class, 'logout']);


Route::get('/admin/products/create', [ProductController::class, 'create']);
Route::post('/admin/products/store', [ProductController::class, 'store']);

Route::get('/admin/products/{id}/edit', [ProductController::class, 'edit']);
Route::put('/admin/products/{id}', [ProductController::class, 'update']);

Route::get('/admin/orders/{id}', [OrderController::class, 'show']);
Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);

Route::post('/checkout', [OrderController::class, 'checkout']);
Route::post('/midtrans/callback', [OrderController::class, 'callback']);

Route::get('/check-payment/{orderId}',[OrderController::class, 'checkPayment']);
Route::get('/admin/pay/{id}', [OrderController::class,'simulatePayment']);



