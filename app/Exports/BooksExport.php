<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BooksExport implements FromCollection, WithHeadings, WithMapping
{
    protected $books;

    public function __construct($books)
    {
        $this->books = $books;
    }

    public function collection()
    {
        return $this->books;
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Detail', 'Status', 'Category', 'Author', 'Created At'];
    }

    public function map($book): array
    {
        return [
            $book->_id ?? $book->id,
            $book->name,
            $book->detail,
            ucfirst($book->status),
            $book->category?->name ?? 'N/A',
            $book->user?->name ?? 'N/A',
            $book->created_at?->format('Y-m-d H:i') ?? '',
        ];
    }
}
