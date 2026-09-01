<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * =========================================================
     * BOOK LIST
     * Search + Filters + Sorting + Pagination
     * =========================================================
     */
    public function index(Request $request)
    {
        $query = Book::with(['category', 'user']);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('detail', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | Category Filter
        |--------------------------------------------------------------------------
        */
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        /*
        |--------------------------------------------------------------------------
        | Tag Filter
        |--------------------------------------------------------------------------
        */
        if ($request->filled('tag_id')) {
            $query->where('tag_ids', $request->tag_id);
        }

        /*
        |--------------------------------------------------------------------------
        | Created From Date
        |--------------------------------------------------------------------------
        */
        if ($request->filled('date_from')) {
            $query->where(
                'created_at',
                '>=',
                now()->parse($request->date_from)->startOfDay()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Created To Date
        |--------------------------------------------------------------------------
        */
        if ($request->filled('date_to')) {
            $query->where(
                'created_at',
                '<=',
                now()->parse($request->date_to)->endOfDay()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Soft Deleted / Active Books
        |--------------------------------------------------------------------------
        */
        if ($request->filled('trashed')) {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
        }

        /*
        |--------------------------------------------------------------------------
        | Advanced Sorting
        |--------------------------------------------------------------------------
        */
        $sort = $request->get('sort', 'newest');

        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;

            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'status':
                $query->orderBy('status', 'asc');
                break;

            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $books = $query
            ->paginate(5)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        $tags = Tag::orderBy('name')->get();

        return view(
            'books.index',
            compact(
                'books',
                'categories',
                'tags'
            )
        );
    }

    /**
     * =========================================================
     * CREATE
     * =========================================================
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        $tags = Tag::orderBy('name')->get();

        return view(
            'books.create',
            compact(
                'categories',
                'tags'
            )
        );
    }

    /**
     * =========================================================
     * STORE
     * =========================================================
     */
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
            $validated['image'] = $request
                ->file('image')
                ->store('books', 'public');
        }

        $validated['user_id'] = auth()->id();

        Book::create($validated);

        return redirect()
            ->route('books.index')
            ->with(
                'success',
                'Book created successfully.'
            );
    }

    /**
     * =========================================================
     * SHOW
     * =========================================================
     */
    public function show(Book $book)
    {
        $book->load([
            'category',
            'user',
            'tags',
        ]);

        return view(
            'books.show',
            compact('book')
        );
    }

    /**
     * =========================================================
     * EDIT
     * =========================================================
     */
    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();

        $tags = Tag::orderBy('name')->get();

        return view(
            'books.edit',
            compact(
                'book',
                'categories',
                'tags'
            )
        );
    }

    /**
     * =========================================================
     * UPDATE
     * =========================================================
     */
    public function update(
        Request $request,
        Book $book
    ) {
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

            if (
                $book->image &&
                Storage::disk('public')->exists($book->image)
            ) {
                Storage::disk('public')->delete(
                    $book->image
                );
            }

            $validated['image'] = $request
                ->file('image')
                ->store('books', 'public');
        }

        $book->update($validated);

        return redirect()
            ->route('books.index')
            ->with(
                'success',
                'Book updated successfully.'
            );
    }

    /**
     * =========================================================
     * DELETE SINGLE BOOK
     * =========================================================
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()
            ->route('books.index')
            ->with(
                'success',
                'Book deleted successfully.'
            );
    }

    /**
     * =========================================================
     * BULK DELETE
     * =========================================================
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'book_ids' => 'required|array|min:1',

            'book_ids.*' => 'required',
        ]);

        $books = Book::whereIn(
            '_id',
            $validated['book_ids']
        )->get();

        $count = 0;

        foreach ($books as $book) {

            /*
            |--------------------------------------------------------------------------
            | Don't delete currently borrowed books
            |--------------------------------------------------------------------------
            */
            if ($book->status === 'borrowed') {
                continue;
            }

            $book->delete();

            $count++;
        }

        if ($count === 0) {
            return back()->with(
                'error',
                'No eligible books were deleted. Borrowed books cannot be bulk deleted.'
            );
        }

        return back()->with(
            'success',
            "{$count} book(s) deleted successfully."
        );
    }

    /**
     * =========================================================
     * RESTORE
     * =========================================================
     */
    public function restore($id)
    {
        $book = Book::onlyTrashed()
            ->findOrFail($id);

        $book->restore();

        return redirect()
            ->route(
                'books.index',
                ['trashed' => 1]
            )
            ->with(
                'success',
                'Book restored successfully.'
            );
    }

    /**
     * =========================================================
     * FORCE DELETE
     * =========================================================
     */
    public function forceDelete($id)
    {
        $book = Book::onlyTrashed()
            ->findOrFail($id);

        if (
            $book->image &&
            Storage::disk('public')->exists($book->image)
        ) {
            Storage::disk('public')->delete(
                $book->image
            );
        }

        $book->forceDelete();

        return redirect()
            ->route(
                'books.index',
                ['trashed' => 1]
            )
            ->with(
                'success',
                'Book permanently deleted.'
            );
    }
}

