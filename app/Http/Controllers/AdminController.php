<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalBooks = Book::count();
        $availableBooks = Book::where('status', 'available')->count();
        $borrowedBooks = Book::where('status', 'borrowed')->count();
        $soldBooks = Book::where('status', 'sold')->count();
        $totalUsers = User::count();
        $recentBooks = Book::latest()->take(5)->get();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalBooks', 'availableBooks', 'borrowedBooks', 'soldBooks',
            'totalUsers', 'recentBooks', 'recentUsers'
        ));
    }

    public function books()
    {
        $books = Book::with(['category', 'user'])->latest()->paginate(20);
        return view('admin.books.index', compact('books'));
    }

    public function users()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }
}
