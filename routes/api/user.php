<?php

use App\Http\Controllers\User\AuthController;

Route::prefix('user')->name('user.')->group(function () {
    Route::post('create', [AuthController::class, 'create']);
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('logout', [AuthController::class, 'logout']);

    Route::post('/forgot-password', [\App\Http\Controllers\User\ForgotPasswordController::class, 'createResetToken']);
    Route::post('/reset-password-token', [\App\Http\Controllers\User\ForgotPasswordController::class, 'resetPassword']);


    Route::get('/', [\App\Http\Controllers\User\UserController::class, 'show'])->name('index');
    Route::delete('/', [\App\Http\Controllers\User\UserController::class, 'destroy'])->name('delete');
    Route::put('edit', [\App\Http\Controllers\User\UserController::class, 'edit'])->name('update');

    Route::get('/orders', [\App\Http\Controllers\User\OrderController::class, 'index'])->middleware('jwt');
});

