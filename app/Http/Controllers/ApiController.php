<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
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

        $books = $query->paginate(15);

        return response()->json($books);
    }

    public function show($id)
    {
        $book = Book::with(['category', 'user', 'tags'])->findOrFail($id);
        return response()->json($book);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'required|string',
            'status' => 'in:available,borrowed,sold',
            'category_id' => 'nullable|exists:categories,_id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('books', 'public');
        }

        $validated['user_id'] = Auth::id();

        $book = Book::create($validated);

        return response()->json($book, 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'required|string',
            'status' => 'in:available,borrowed,sold',
            'category_id' => 'nullable|exists:categories,_id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($book->image && \Storage::disk('public')->exists($book->image)) {
                \Storage::disk('public')->delete($book->image);
            }
            $validated['image'] = $request->file('image')->store('books', 'public');
        }

        $book->update($validated);

        return response()->json($book);
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(null, 204);
    }

    public function categories()
    {
        return response()->json(Category::orderBy('name')->get());
    }

    public function tags()
    {
        return response()->json(Tag::orderBy('name')->get());
    }
}
