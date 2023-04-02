<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\ForgotPasswordController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\UserController;

Route::prefix('user')->name('user.')->group(function () {
    Route::post('create', [AuthController::class, 'create']);
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('logout', [AuthController::class, 'logout']);

    Route::post(
        '/forgot-password',
        [
            ForgotPasswordController::class,
            'createResetToken',
        ]
    );
    Route::post(
        '/reset-password-token',
        [
            ForgotPasswordController::class,
            'resetPassword',
        ]
    );

    Route::get('/', [UserController::class, 'show'])->name('index');
    Route::delete('/', [UserController::class, 'destroy'])->name('delete');
    Route::put('edit', [UserController::class, 'edit'])->name('update');

    Route::get('/orders', [OrderController::class, 'index'])
        ->middleware('jwt');
});
