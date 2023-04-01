<?php

use App\Http\Controllers\ProductController;

Route::apiResource('product', ProductController::class)->middleware('jwt');
