<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Book extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'books';

    protected $fillable = [
        'name',
        'detail',
    ];
}
