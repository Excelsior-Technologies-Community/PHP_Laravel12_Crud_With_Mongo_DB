<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';

    protected $collection = 'books';

    protected $fillable = [
        'name',
        'detail',
        'image',
        'status',
        'category_id',
        'tag_ids',
        'user_id',
    ];

    protected $casts = [
        'tag_ids' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($book) {
            if (!$book->status) {
                $book->status = 'available';
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, null, 'tag_ids', '_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * All borrowing records for this book.
     */
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class, 'book_id');
    }

    /**
     * Current active borrowing.
     */
    public function activeBorrowing()
    {
        return $this->hasOne(Borrowing::class, 'book_id')
            ->where('status', 'borrowed');
    }
}