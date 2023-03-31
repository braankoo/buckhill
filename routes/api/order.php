<?php

use App\Http\Controllers\OrderController;

Route::apiResource('order', OrderController::class)->middleware('jwt');
Route::get('order/{order}/download', [OrderController::class, 'download'])->middleware('jwt');
Route::get('orders/shipment-locator', [OrderController::class, 'shipmentLocator'])->middleware('jwt');
Route::get('orders/dashboard', [OrderController::class, 'shipmentLocator'])->middleware('jwt');
