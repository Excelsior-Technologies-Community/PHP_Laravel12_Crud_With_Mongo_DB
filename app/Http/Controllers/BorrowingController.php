<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    /**
     * Maximum books a user can borrow.
     */
    private const MAX_ACTIVE_BORROWINGS = 3;

    /**
     * Number of days allowed for borrowing.
     */
    private const BORROWING_DAYS = 14;

    /**
     * =========================================================
     * MY BORROWING HISTORY
     * =========================================================
     */
    public function index()
    {
        $borrowings = Borrowing::with([
                'book',
                'user'
            ])
            ->where(
                'user_id',
                Auth::id()
            )
            ->oldest('borrowed_at')
            ->paginate(5);

        return view(
            'borrowings.index',
            compact('borrowings')
        );
    }

    /**
     * =========================================================
     * ALL BORROWINGS
     * =========================================================
     */
    public function all()
    {
        $borrowings = Borrowing::with([
                'book',
                'user'
            ])
            ->oldest('borrowed_at')
            ->paginate(5);

        return view(
            'borrowings.all',
            compact('borrowings')
        );
    }

    /**
     * =========================================================
     * BORROW BOOK
     * =========================================================
     */
    public function borrow(Book $book)
    {
        /*
        |--------------------------------------------------------------------------
        | Prevent deleted book
        |--------------------------------------------------------------------------
        */
        if ($book->trashed()) {
            return back()->with(
                'error',
                'This book has been deleted.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Book must be available
        |--------------------------------------------------------------------------
        */
        if ($book->status !== 'available') {
            return back()->with(
                'error',
                'This book is not currently available.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | NEW FEATURE:
        | Maximum 3 active borrowings
        |--------------------------------------------------------------------------
        */
        $activeBorrowings = Borrowing::where(
                'user_id',
                Auth::id()
            )
            ->where(
                'status',
                'borrowed'
            )
            ->count();

        if (
            $activeBorrowings >=
            self::MAX_ACTIVE_BORROWINGS
        ) {
            return back()->with(
                'error',
                'You can borrow a maximum of ' .
                self::MAX_ACTIVE_BORROWINGS .
                ' books at a time.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate active borrowing
        |--------------------------------------------------------------------------
        */
        $existingBorrowing = Borrowing::where(
                'book_id',
                $book->_id
            )
            ->where(
                'status',
                'borrowed'
            )
            ->first();

        if ($existingBorrowing) {
            return back()->with(
                'error',
                'This book is already borrowed.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create borrowing
        |--------------------------------------------------------------------------
        */
        Borrowing::create([
            'book_id' => $book->_id,

            'user_id' => Auth::id(),

            'borrowed_at' => now(),

            'due_at' => now()->addDays(
                self::BORROWING_DAYS
            ),

            'status' => 'borrowed',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update book status
        |--------------------------------------------------------------------------
        */
        $book->update([
            'status' => 'borrowed',
        ]);

        return back()->with(
            'success',
            'Book borrowed successfully. Due date: ' .
            now()
                ->addDays(self::BORROWING_DAYS)
                ->format('Y-m-d')
        );
    }

    /**
     * =========================================================
     * RETURN BOOK
     * =========================================================
     */
    public function returnBook(
        Borrowing $borrowing
    ) {
        if ($borrowing->status !== 'borrowed') {
            return back()->with(
                'error',
                'This borrowing record has already been returned.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Only borrower or admin
        |--------------------------------------------------------------------------
        */
        if (
            !Auth::user()->isAdmin() &&
            (string) $borrowing->user_id !==
            (string) Auth::id()
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Return borrowing
        |--------------------------------------------------------------------------
        */
        $borrowing->update([
            'status' => 'returned',

            'returned_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Restore book availability
        |--------------------------------------------------------------------------
        */
        $book = Book::withTrashed()
            ->find($borrowing->book_id);

        if ($book) {
            $book->update([
                'status' => 'available',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Show overdue message
        |--------------------------------------------------------------------------
        */
        if ($borrowing->isOverdue()) {
            return back()->with(
                'success',
                'Book returned successfully. This book was returned late.'
            );
        }

        return back()->with(
            'success',
            'Book returned successfully.'
        );
    }

    /**
     * =========================================================
     * BOOK BORROWING HISTORY
     * =========================================================
     */
    public function bookHistory(Book $book)
    {
        $borrowings = Borrowing::with('user')
            ->where(
                'book_id',
                $book->_id
            )
            ->latest('borrowed_at')
            ->paginate(15);

        return view(
            'borrowings.book-history',
            compact(
                'book',
                'borrowings'
            )
        );
    }
}
