<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ExportController;

Route::get('/', fn() => redirect('/books'));

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Books
Route::resource('books', BookController::class);
Route::post('books/{id}/restore', [BookController::class, 'restore'])->name('books.restore');
Route::delete('books/{id}/force-delete', [BookController::class, 'forceDelete'])->name('books.force-delete');

// Export
Route::get('books/export/csv', [ExportController::class, 'exportCsv'])->name('books.export.csv');
Route::get('books/export/pdf', [ExportController::class, 'exportPdf'])->name('books.export.pdf');

// Categories & Tags
Route::resource('categories', CategoryController::class)->except(['show']);
Route::resource('tags', TagController::class)->except(['show']);

// Admin
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/books', [AdminController::class, 'books'])->name('books.index');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
});
