<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\ForgotPasswordController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\UserController;

Route::apiResource('user', UserController::class)->only(['show', 'edit', 'destroy']);

Route::name('user.')->group(function () {
    Route::post('/user/create', [AuthController::class, 'create']);
    Route::post('/user/login', [AuthController::class, 'login'])->name('login');
    Route::get('/user/logout', [AuthController::class, 'logout']);

    Route::post(
        '/user/forgot-password',
        [
            ForgotPasswordController::class,
            'createResetToken',
        ]
    );
    Route::post(
        '/user/reset-password-token',
        [
            ForgotPasswordController::class,
            'resetPassword',
        ]
    );
    Route::get('/user/orders', [OrderController::class, 'index'])->name('order.index');
});
