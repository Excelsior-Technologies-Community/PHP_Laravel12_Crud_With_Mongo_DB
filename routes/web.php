<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\AnalyticsController;


/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('books.index');
});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');


/*
|--------------------------------------------------------------------------
| Books
|--------------------------------------------------------------------------
*/

Route::resource('books', BookController::class);

Route::post(
    'books/{id}/restore',
    [BookController::class, 'restore']
)->name('books.restore');

Route::delete(
    'books/{id}/force-delete',
    [BookController::class, 'forceDelete']
)->name('books.force-delete');


/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/

Route::resource('categories', CategoryController::class)
    ->except(['show']);


/*
|--------------------------------------------------------------------------
| Tags
|--------------------------------------------------------------------------
*/

Route::resource('tags', TagController::class)
    ->except(['show']);


/*
|--------------------------------------------------------------------------
| Book Export
|--------------------------------------------------------------------------
*/

Route::get(
    'books/export/csv',
    [ExportController::class, 'exportCsv']
)->name('books.export.csv');

Route::get(
    'books/export/pdf',
    [ExportController::class, 'exportPdf']
)->name('books.export.pdf');


/*
|--------------------------------------------------------------------------
| Authenticated User Features
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |----------------------------------------------------------------------
    | Borrow Book
    |----------------------------------------------------------------------
    */

    Route::post(
        'books/{book}/borrow',
        [BorrowingController::class, 'borrow']
    )->name('books.borrow');


    /*
    |----------------------------------------------------------------------
    | My Borrowings
    |----------------------------------------------------------------------
    */

    Route::get(
        'borrowings',
        [BorrowingController::class, 'index']
    )->name('borrowings.index');


    /*
    |----------------------------------------------------------------------
    | Return Book
    |----------------------------------------------------------------------
    */

    Route::post(
        'borrowings/{borrowing}/return',
        [BorrowingController::class, 'returnBook']
    )->name('borrowings.return');


    /*
    |----------------------------------------------------------------------
    | Book Borrowing History
    |----------------------------------------------------------------------
    */

    Route::get(
        'books/{book}/borrowing-history',
        [BorrowingController::class, 'bookHistory']
    )->name('books.borrowing-history');


    /*
    |----------------------------------------------------------------------
    | Admin Borrowings
    |----------------------------------------------------------------------
    */

    Route::get(
        'admin/borrowings',
        [BorrowingController::class, 'all']
    )->name('admin.borrowings.index');


    /*
    |----------------------------------------------------------------------
    | MongoDB Analytics
    |----------------------------------------------------------------------
    */

    Route::get(
        'admin/analytics',
        [AnalyticsController::class, 'index']
    )->name('admin.analytics');

});


/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {

        Route::get(
            '/dashboard',
            [AdminController::class, 'dashboard']
        )->name('dashboard');

        Route::get(
            '/books',
            [AdminController::class, 'books']
        )->name('books.index');

        Route::get(
            '/users',
            [AdminController::class, 'users']
        )->name('users.index');
    });