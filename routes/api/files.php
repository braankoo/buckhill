<?php

Route::post('/file/upload',[\App\Http\Controllers\FileController::class,'store']);
Route::get('/file/{file}',[\App\Http\Controllers\FileController::class,'show']);

