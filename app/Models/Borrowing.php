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
        'due_at',
        'returned_at',
        'status',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
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

            /*
            |--------------------------------------------------------------------------
            | Default due date = 14 days
            |--------------------------------------------------------------------------
            */
            if (!$borrowing->due_at) {
                $borrowing->due_at = now()->addDays(14);
            }
        });
    }

    /**
     * Book relationship.
     */
    public function book()
    {
        return $this->belongsTo(
            Book::class,
            'book_id'
        );
    }

    /**
     * User relationship.
     */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * Check whether borrowing is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'borrowed'
            && $this->due_at
            && $this->due_at->isPast();
    }

    /**
     * Days remaining.
     */
    public function daysRemaining(): int
    {
        if (!$this->due_at) {
            return 0;
        }

        return max(
            0,
            now()->diffInDays(
                $this->due_at,
                false
            )
        );
    }
}
