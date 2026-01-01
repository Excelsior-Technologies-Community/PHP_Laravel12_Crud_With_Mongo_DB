<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::get('/', function () {
    return redirect('/books');
});

Route::resource('books', BookController::class);
