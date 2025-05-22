<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\CustomerController;
use App\Models\OrderController;
use App\Models\PaymentController;
use App\Models\ProductController;
use App\Models\RefusedOrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('/create')->group(function () {
    Route::post('/customer', [CustomerController::class, 'create']);
    Route::post('/order', [OrderController::class, 'create']);
    Route::post('/payment', [PaymentController::class, 'create']);
    Route::post('/product', [ProductController::class, 'create']);
    Route::post('/refused-order', [RefusedOrderController::class, 'create']);
});

Route::pefix('/list')->group(function () {
    Route::get('/customer', [CustomerController::class, 'list']);
    Route::get('/order', [OrderController::class, 'list']);
    Route::get('/payment', [PaymentController::class, 'list']);
    Route::get('/product', [ProductController::class, 'list']);
    route::get('/refused-order', [RefusedOrderController::class, 'list']);
});

Route::prefix('/edit')->group(function () {
    Route::put('/customer/{id}', [CustomerController::class, 'edit']);
    Route::put('/order/{id}', [OrderController::class, 'edit']);
    Route::put('/payment/{id}', [PaymentController::class, 'edit']);
    Route::put('/product/{id}', [ProductController::class, 'edit']);
    Route::put('/refused-order/{id}', [RefusedOrderController::class, 'edit']);
});

Route::prefix('/delete')->group(function () {
    Route::delete('/customer/{id}', [CustomerController::class, 'delete']);
    Route::delete('/order/{id}', [OrderController::class, 'delete']);
    Route::delete('/payment/{id}', [PaymentController::class, 'delete']);
    Route::delete('/product/{id}', [ProductController::class, 'delete']);
    Route::delete('/refused-order/{id}', [RefusedOrderController::class, 'delete']);
});