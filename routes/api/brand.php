<?php

use App\Http\Controllers\BrandController;

Route::get('brand', [BrandController::class, 'index']);
Route::get('brand/{brand}', [BrandController::class, 'show']);
Route::middleware('jwt')->group(function () {
    Route::post('brand', [BrandController::class, 'store']);
    Route::put('brand/{brand}', [BrandController::class, 'update']);
    Route::delete('brand/{brand}', [BrandController::class, 'store']);
});
