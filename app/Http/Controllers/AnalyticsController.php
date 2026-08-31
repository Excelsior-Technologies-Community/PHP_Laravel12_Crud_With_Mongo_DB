<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * MongoDB analytics dashboard.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Basic Book Statistics
        |--------------------------------------------------------------------------
        */

        $totalBooks = Book::count();

        $availableBooks = Book::where(
            'status',
            'available'
        )->count();

        $borrowedBooks = Book::where(
            'status',
            'borrowed'
        )->count();

        $soldBooks = Book::where(
            'status',
            'sold'
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Borrowing Statistics
        |--------------------------------------------------------------------------
        */

        $totalBorrowings = Borrowing::count();

        $activeBorrowings = Borrowing::where(
            'status',
            'borrowed'
        )->count();

        $returnedBorrowings = Borrowing::where(
            'status',
            'returned'
        )->count();

        /*
        |--------------------------------------------------------------------------
        | MongoDB Aggregation:
        | Books by Status
        |--------------------------------------------------------------------------
        */

        $booksByStatus = Book::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$group' => [
                        '_id' => '$status',
                        'total' => [
                            '$sum' => 1,
                        ],
                    ],
                ],
                [
                    '$sort' => [
                        'total' => -1,
                    ],
                ],
            ]);
        });

        $booksByStatus = collect($booksByStatus);

        /*
        |--------------------------------------------------------------------------
        | MongoDB Aggregation:
        | Books by Category
        |--------------------------------------------------------------------------
        */

        $booksByCategory = Book::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'category_id' => [
                            '$ne' => null,
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '$category_id',
                        'total' => [
                            '$sum' => 1,
                        ],
                    ],
                ],
                [
                    '$sort' => [
                        'total' => -1,
                    ],
                ],
            ]);
        });

        $booksByCategory = collect($booksByCategory);

        /*
        |--------------------------------------------------------------------------
        | Add category names
        |--------------------------------------------------------------------------
        */

        $categoryIds = $booksByCategory
            ->pluck('_id')
            ->filter()
            ->values()
            ->all();

        $categories = Category::whereIn(
            '_id',
            $categoryIds
        )->get()->keyBy(function ($category) {
            return (string) $category->_id;
        });

        $booksByCategory = $booksByCategory->map(
            function ($item) use ($categories) {

                $categoryId = (string) $item->_id;

                return [
                    'name' => $categories[$categoryId]->name
                        ?? 'Unknown',
                    'total' => $item->total,
                ];
            }
        );

        /*
        |--------------------------------------------------------------------------
        | MongoDB Aggregation:
        | Most Borrowed Books
        |--------------------------------------------------------------------------
        */

        $mostBorrowedBooks = Borrowing::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$group' => [
                        '_id' => '$book_id',
                        'total_borrowed' => [
                            '$sum' => 1,
                        ],
                    ],
                ],
                [
                    '$sort' => [
                        'total_borrowed' => -1,
                    ],
                ],
                [
                    '$limit' => 10,
                ],
            ]);
        });

        $mostBorrowedBooks = collect($mostBorrowedBooks);

        /*
        |--------------------------------------------------------------------------
        | Attach Book Information
        |--------------------------------------------------------------------------
        */

        $bookIds = $mostBorrowedBooks
            ->pluck('_id')
            ->filter()
            ->values()
            ->all();

        $bookModels = Book::whereIn(
            '_id',
            $bookIds
        )->get()->keyBy(function ($book) {
            return (string) $book->_id;
        });

        $mostBorrowedBooks = $mostBorrowedBooks->map(
            function ($item) use ($bookModels) {

                $bookId = (string) $item->_id;

                return [
                    'name' => $bookModels[$bookId]->name
                        ?? 'Unknown Book',
                    'total_borrowed' => $item->total_borrowed,
                ];
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Monthly Borrowing Statistics
        |--------------------------------------------------------------------------
        */

        $monthlyBorrowings = Borrowing::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'borrowed_at' => [
                            '$gte' => now()
                                ->subMonths(6)
                                ->startOfMonth()
                                ->toDateTime(),
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id' => [
                            'year' => [
                                '$year' => '$borrowed_at',
                            ],
                            'month' => [
                                '$month' => '$borrowed_at',
                            ],
                        ],
                        'total' => [
                            '$sum' => 1,
                        ],
                    ],
                ],
                [
                    '$sort' => [
                        '_id.year' => 1,
                        '_id.month' => 1,
                    ],
                ],
            ]);
        });

        $monthlyBorrowings = collect($monthlyBorrowings);

        /*
        |--------------------------------------------------------------------------
        | Return Dashboard
        |--------------------------------------------------------------------------
        */

        return view(
            'analytics.index',
            compact(
                'totalBooks',
                'availableBooks',
                'borrowedBooks',
                'soldBooks',
                'totalBorrowings',
                'activeBorrowings',
                'returnedBorrowings',
                'booksByStatus',
                'booksByCategory',
                'mostBorrowedBooks',
                'monthlyBorrowings'
            )
        );
    }
}