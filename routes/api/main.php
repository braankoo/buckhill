<?php

use App\Http\Controllers\MainPage\Blog;
use App\Http\Controllers\MainPage\Promotion;

Route::name('main.')->prefix('main')->group(function () {
    Route::get('promotions', [Promotion::class, 'index'])->name('promotion.index');

    Route::name('blog.')->group(function () {
        Route::get('blog', [Blog::class, 'index'])->name('index');
        Route::get('blog/{post}', [Blog::class, 'single'])->name('single');
    });
});
