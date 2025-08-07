<?php

use App\Http\Controllers\Auth\TokenController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::post('/tokens', [TokenController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('tokens', TokenController::class)->only(['destroy']);
        Route::apiResource('transactions', TransactionController::class)->only(['store', 'index', 'show']);
    });
});
