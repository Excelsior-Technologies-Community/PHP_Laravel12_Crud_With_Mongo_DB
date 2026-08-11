<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Category extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function booted(): void
    {
        static::creating(function ($category) {
            if (!$category->slug && $category->name) {
                $category->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $category->name)));
            }
        });
    }

    public function books()
    {
        return $this->hasMany(Book::class, 'category_id');
    }
}
