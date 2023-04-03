<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('create', [AuthController::class, 'create'])->name('create');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::name('user.')->group(function () {
        Route::get('user-listing', [UserController::class, 'index'])
            ->name('index');

        Route::put('user-listing/{user}', [UserController::class, 'update'])
            ->name('update');

        Route::delete('user-listing/{user}', [UserController::class, 'destroy'])
            ->name('delete');
    });
});
