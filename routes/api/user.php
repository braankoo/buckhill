<?php

use App\Http\Controllers\User\AuthController;

Route::prefix('user')->group(function () {
    Route::post('create', [AuthController::class, 'create']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('jwt');

    Route::post('/forgot-password', [\App\Http\Controllers\User\ForgotPasswordController::class, 'createResetToken']);
    Route::post('/reset-password-token', [\App\Http\Controllers\User\ForgotPasswordController::class, 'resetPassword']);




    Route::get('/', [\App\Http\Controllers\User\UserController::class, 'show'])->middleware('jwt');
    Route::delete('/', [\App\Http\Controllers\User\UserController::class, 'destroy'])->middleware('jwt');
    Route::put('edit', [\App\Http\Controllers\User\UserController::class, 'edit'])->middleware('jwt');

    Route::get('/orders', [\App\Http\Controllers\User\OrderController::class, 'index'])->middleware('jwt');
});

