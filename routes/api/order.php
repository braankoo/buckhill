<?php

use App\Http\Controllers\OrderController;

Route::apiResource('order', OrderController::class);

Route::name('orders.')->prefix('orders')->group(function () {
    Route::get('{order}/download', [OrderController::class, 'download'])->name('download');
    Route::get('shipment-locator', [OrderController::class, 'shipmentLocator'])->name('locator');
    Route::get('dashboard', [OrderController::class, 'dashboard'])->name('dashboard');
});

