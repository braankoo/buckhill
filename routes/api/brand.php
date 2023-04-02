<?php

use App\Http\Controllers\BrandController;

Route::name('brand.')->group(function () {
    Route::get(
        'brand',
        [BrandController::class, 'index']
    )->name('index');

    Route::get(
        'brand/{brand}',
        [BrandController::class, 'show']
    )->name('show');

    Route::middleware('jwt')->group(function () {
        Route::post(
            'brand',
            [BrandController::class, 'store']
        )->name('store');

        Route::put(
            'brand/{brand}',
            [BrandController::class, 'update']
        )->name(
            'update'
        );

        Route::delete(
            'brand/{brand}',
            [BrandController::class, 'destroy']
        )->name('destroy');
    });
});
