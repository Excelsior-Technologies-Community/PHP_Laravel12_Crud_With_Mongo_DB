<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function exportCsv(Request $request)
    {
        $fileName = 'books_' . date('Y-m-d_H-i-s') . '.csv';
        $books = Book::with(['category', 'user'])->get();

        return Excel::download(new \App\Exports\BooksExport($books), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $books = Book::with(['category', 'user'])->get();
        $pdf = Pdf::loadView('exports.books-pdf', compact('books'));
        return $pdf->download('books_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
