<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['category', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('detail', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('tag_id')) {
            $query->where('tag_ids', $request->tag_id);
        }

        if ($request->filled('trashed')) {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
        }

        $books = $query->latest()->paginate(10)->appends($request->query());

        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('books.index', compact('books', 'categories', 'tags'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('books.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'required|string',
            'status' => 'required|in:available,borrowed,sold',
            'category_id' => 'nullable|exists:categories,_id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('books', 'public');
        }

        $validated['user_id'] = auth()->id();

        Book::create($validated);

        return redirect()->route('books.index')
            ->with('success', 'Book created successfully');
    }

    public function show(Book $book)
    {
        $book->load(['category', 'user', 'tags']);
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('books.edit', compact('book', 'categories', 'tags'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'required|string',
            'status' => 'required|in:available,borrowed,sold',
            'category_id' => 'nullable|exists:categories,_id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($book->image && Storage::disk('public')->exists($book->image)) {
                Storage::disk('public')->delete($book->image);
            }
            $validated['image'] = $request->file('image')->store('books', 'public');
        }

        $book->update($validated);

        return redirect()->route('books.index')
            ->with('success', 'Book updated successfully');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully');
    }

    public function restore($id)
    {
        $book = Book::onlyTrashed()->findOrFail($id);
        $book->restore();

        return redirect()->route('books.index', ['trashed' => 1])
            ->with('success', 'Book restored successfully');
    }

    public function forceDelete($id)
    {
        $book = Book::onlyTrashed()->findOrFail($id);

        if ($book->image && Storage::disk('public')->exists($book->image)) {
            Storage::disk('public')->delete($book->image);
        }

        $book->forceDelete();

        return redirect()->route('books.index', ['trashed' => 1])
            ->with('success', 'Book permanently deleted');
    }
}
