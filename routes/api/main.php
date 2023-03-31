<?php

use App\Http\Controllers\MainPage\Blog;
use App\Http\Controllers\MainPage\Promotion;

Route::prefix('main')->group(function () {
    Route::get('promotions', [Promotion::class, 'index']);
    Route::get('blog', [Blog::class, 'index']);
    Route::get('blog/{post}', [Blog::class, 'single']);
});
