<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Models\User;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('create', [AuthController::class, 'create'])->name('create');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('logout', [AuthController::class, 'logout'])->name(
        'logout'
    )->middleware('api');

    Route::name('user.')->group(function () {
        Route::get('user-listing', [UserController::class, 'index'])
            ->can('viewAny', User::class)
            ->name('index');

        Route::put('user-listing/{user}', [UserController::class, 'update'])
            ->can('update', User::class)
            ->name('update');

        Route::delete('user-listing/{user}', [UserController::class, 'destroy'])
            ->can('delete', User::class)
            ->name('delete');
    });
});
