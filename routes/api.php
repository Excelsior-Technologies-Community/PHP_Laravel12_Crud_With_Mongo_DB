<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

// Public API
Route::get('/books', [ApiController::class, 'index']);
Route::get('/books/{id}', [ApiController::class, 'show']);
Route::get('/categories', [ApiController::class, 'categories']);
Route::get('/tags', [ApiController::class, 'tags']);

// Protected API (requires Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/books', [ApiController::class, 'store']);
    Route::put('/books/{id}', [ApiController::class, 'update']);
    Route::delete('/books/{id}', [ApiController::class, 'destroy']);
});
