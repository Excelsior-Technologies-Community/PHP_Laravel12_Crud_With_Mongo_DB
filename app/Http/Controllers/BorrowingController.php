<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    /**
     * Show current user's borrowing history.
     */
    public function index()
    {
        $borrowings = Borrowing::with(['book', 'user'])
            ->where('user_id', Auth::id())
            ->latest('borrowed_at')
            ->paginate(10);

        return view('borrowings.index', compact('borrowings'));
    }

    /**
     * Show all borrowing records.
     *
     * Useful for administrators.
     */
    public function all()
    {
        $borrowings = Borrowing::with(['book', 'user'])
            ->latest('borrowed_at')
            ->paginate(20);

        return view('borrowings.all', compact('borrowings'));
    }

    /**
     * Borrow a book.
     */
    public function borrow(Book $book)
    {
        /*
         * Prevent users from borrowing a soft-deleted book.
         */
        if ($book->trashed()) {
            return back()->with(
                'error',
                'This book has been deleted.'
            );
        }

        /*
         * Only available books can be borrowed.
         */
        if ($book->status !== 'available') {
            return back()->with(
                'error',
                'This book is not currently available.'
            );
        }

        /*
         * Prevent the same book from having
         * multiple active borrowing records.
         */
        $existingBorrowing = Borrowing::where('book_id', $book->_id)
            ->where('status', 'borrowed')
            ->first();

        if ($existingBorrowing) {
            return back()->with(
                'error',
                'This book is already borrowed.'
            );
        }

        /*
         * Create borrowing record.
         */
        Borrowing::create([
            'book_id' => $book->_id,
            'user_id' => Auth::id(),
            'borrowed_at' => now(),
            'status' => 'borrowed',
        ]);

        /*
         * Update book status.
         */
        $book->update([
            'status' => 'borrowed',
        ]);

        return back()->with(
            'success',
            'Book borrowed successfully.'
        );
    }

    /**
     * Return a borrowed book.
     */
    public function returnBook(Borrowing $borrowing)
    {
        /*
         * Prevent returning an already returned book.
         */
        if ($borrowing->status !== 'borrowed') {
            return back()->with(
                'error',
                'This borrowing record has already been returned.'
            );
        }

        /*
         * Only the borrower or an administrator
         * can return the book.
         */
        if (
            !Auth::user()->isAdmin() &&
            (string) $borrowing->user_id !== (string) Auth::id()
        ) {
            abort(403);
        }

        /*
         * Update borrowing record.
         */
        $borrowing->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        /*
         * Find the related book.
         *
         * withTrashed() is important here because the book
         * may have been soft-deleted while it was borrowed.
         */
        $book = Book::withTrashed()->find($borrowing->book_id);

        if ($book) {
            $book->update([
                'status' => 'available',
            ]);
        }

        return back()->with(
            'success',
            'Book returned successfully.'
        );
    }

    /**
     * Show borrowing history for a particular book.
     */
    public function bookHistory(Book $book)
    {
        $borrowings = Borrowing::with('user')
            ->where('book_id', $book->_id)
            ->latest('borrowed_at')
            ->paginate(15);

        return view(
            'borrowings.book-history',
            compact('book', 'borrowings')
        );
    }
}