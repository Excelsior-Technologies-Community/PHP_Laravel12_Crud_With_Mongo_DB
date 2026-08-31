<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Borrowing extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'borrowings';

    protected $fillable = [
        'book_id',
        'user_id',
        'borrowed_at',
        'returned_at',
        'status',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($borrowing) {
            if (!$borrowing->status) {
                $borrowing->status = 'borrowed';
            }

            if (!$borrowing->borrowed_at) {
                $borrowing->borrowed_at = now();
            }
        });
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}