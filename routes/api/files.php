<?php

Route::name('file.')->group(function () {
    Route::post(
        '/file/upload',
        [\App\Http\Controllers\FileController::class, 'store']
    )
        ->name('upload');

    Route::get(
        '/file/{file}',
        [\App\Http\Controllers\FileController::class, 'show']
    )
        ->name('show');
});
