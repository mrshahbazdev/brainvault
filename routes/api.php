<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Bookmarks API
    Route::apiResource('bookmarks', \App\Http\Controllers\Api\BookmarkController::class);

    // Collections API
    Route::apiResource('collections', \App\Http\Controllers\Api\CollectionController::class);

    // Notes API
    Route::apiResource('notes', \App\Http\Controllers\Api\NoteController::class);

    // Highlights API
    Route::apiResource('highlights', \App\Http\Controllers\Api\HighlightController::class);

    // Tags API
    Route::apiResource('tags', \App\Http\Controllers\Api\TagController::class);

    // Search & AI
    Route::get('/search', [\App\Http\Controllers\Api\SearchController::class, 'search']);
    Route::get('/search/related', [\App\Http\Controllers\Api\SearchController::class, 'related']);
    Route::post('/ask', [\App\Http\Controllers\Api\SearchController::class, 'ask']);
});
