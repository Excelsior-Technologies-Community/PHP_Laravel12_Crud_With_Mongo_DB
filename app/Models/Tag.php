<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Tag extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tags';

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function booted(): void
    {
        static::creating(function ($tag) {
            if (!$tag->slug && $tag->name) {
                $tag->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tag->name)));
            }
        });
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, null, '_id', 'tag_ids');
    }
}
