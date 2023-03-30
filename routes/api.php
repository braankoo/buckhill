<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::post('create', [AuthController::class, 'create'])->name('create');
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');

        Route::middleware(['jwt'])->name('user')->group(function () {
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
    })->name('admin.');

    Route::prefix('user')->group(function () {
        Route::post('create', [\App\Http\Controllers\User\RegisterController::class, 'create']);
    });
});
