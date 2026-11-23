<?php

use App\Http\Controllers\ArticleManagementController;
use App\Http\Controllers\ArticlePageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ArticlePageController::class, 'index'])->name('articles.index');
Route::get('/articles/{article:permalink}', [ArticlePageController::class, 'show'])->name('articles.show');
Route::get('/article-photos/{filename}', [ArticlePageController::class, 'photo'])
    ->where('filename', '[A-Za-z0-9]{40}\.(?:jpe?g|png|webp)')
    ->name('articles.photo');

Route::prefix('manage')->name('manage.')->group(function () {
    Route::resource('articles', ArticleManagementController::class);
});
