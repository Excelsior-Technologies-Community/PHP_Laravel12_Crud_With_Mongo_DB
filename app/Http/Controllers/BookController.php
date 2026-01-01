<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // LIST
    public function index()
    {
        $books = Book::latest()->get();
        return view('books.index', compact('books'));
    }

    // CREATE FORM
    public function create()
    {
        return view('books.create');
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required',
            'detail' => 'required',
        ]);

        Book::create($request->all());

        return redirect()->route('books.index')
            ->with('success', 'Book created successfully');
    }

    // SHOW
    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    // EDIT FORM
    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    // UPDATE
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'name'   => 'required',
            'detail' => 'required',
        ]);

        $book->update($request->all());

        return redirect()->route('books.index')
            ->with('success', 'Book updated successfully');
    }

    // DELETE
    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully');
    }
}
