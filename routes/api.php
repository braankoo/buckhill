<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\LoginController;
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

        Route::post('create', [RegisterController::class, 'create'])->name('create');
        Route::post('login', [LoginController::class, 'login'])->name('login');
        Route::get('logout', [LoginController::class, 'logout'])->name('logout');

        Route::middleware(['jwt'])->name('user')->group(function () {
            Route::get('user-listing', [UserController::class, 'index'])
                ->name('index');

            Route::put('user-listing/{user}', [UserController::class, 'update'])
                ->name('update');

            Route::delete('user-listing/{user}', [UserController::class, 'destroy'])
                ->name('delete');
        });
    })->name('admin.');
});
