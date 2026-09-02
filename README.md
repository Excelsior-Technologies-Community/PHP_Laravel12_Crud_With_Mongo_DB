# PHP_Laravel12_Crud_With_Mongo_DB

# Step 1 : Install Laravel 12 and Create Project
```php
composer create-project laravel/laravel PHP_Laravel12_Crud_With_Mongo_DB
```
# Step 2: Open  Folder 
```php
cd PHP_Laravel12_Crud_With_Mongo_DB folder
```
# Step 3 : Install MangoDb Laravel Package
```php
composer require mongodb/laravel-mongodb
```
# Step 4 : Set up For.env file database
# ===============================
# DATABASE (MongoDB)
# ===============================
```php
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=hddatabase
DB_USERNAME=
DB_PASSWORD=
```
# Step 5 : config/database.php Add MongoDB Connection
```php
 'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', 27017),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'options'  => [
                'database' => 'admin',
            ],
```
# Step 6 : Create Book Model (MongoDB)
```php
php artisan make:model Book
```
```php
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
```
# Step 7 : Create Route for web.php file
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::get('/', function () {
    return redirect('/books');
});

Route::resource('books', BookController::class);
```
# Step 8 : Create Book Controller
```php
php artisan make:controller BookController
```
```php
<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // LIST
    public function index()
    {
        $books = Book::latest()->get();
        return view('books.index', compact('books'));
    }

    // CREATE FORM
    public function create()
    {
        return view('books.create');
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required',
            'detail' => 'required',
        ]);

        Book::create($request->all());

        return redirect()->route('books.index')
            ->with('success', 'Book created successfully');
    }

    // SHOW
    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    // EDIT FORM
    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    // UPDATE
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'name'   => 'required',
            'detail' => 'required',
        ]);

        $book->update($request->all());

        return redirect()->route('books.index')
            ->with('success', 'Book updated successfully');
    }

    // DELETE
    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully');
    }
}
```
# Step 9 : Now Also Create All blade file and layout file in resource/view/books folder
# resource/view/books /layout.blade.php
```php
<!DOCTYPE html>
<html>
<head>
    <title>Laravel 12 MongoDB CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    @yield('content')
</div>

</body>
</html>

```
# resource/view/books /layout index.blade.php
```php
@extends('books.layout')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h3>Books List</h3>
    <a class="btn btn-success" href="{{ route('books.create') }}">Add New Book</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>Detail</th>
        <th width="250">Action</th>
    </tr>

    @foreach($books as $book)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $book->name }}</td>
        <td>{{ $book->detail }}</td>
        <td>
            <a class="btn btn-info btn-sm" href="{{ route('books.show',$book->_id) }}">Show</a>
            <a class="btn btn-primary btn-sm" href="{{ route('books.edit',$book->_id) }}">Edit</a>

            <form action="{{ route('books.destroy',$book->_id) }}"
                  method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm"
                        onclick="return confirm('Delete?')">
                    Delete
                </button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

@endsection
```
# resource/view/books /layout create.blade.php
```php
@extends('books.layout')

@section('content')

<h3>Add New Book</h3>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('books.store') }}">
    @csrf

    <div class="mb-2">
        <label>Name</label>
        <input type="text" name="name" class="form-control">
    </div>

    <div class="mb-2">
        <label>Detail</label>
        <textarea name="detail" class="form-control"></textarea>
    </div>

    <button class="btn btn-primary">Save</button>
    <a href="{{ route('books.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection
```
# resource/view/books /layout edit.blade.php
```php
@extends('books.layout')

@section('content')

<h3>Edit Book</h3>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('books.update',$book->_id) }}">
    @csrf
    @method('PUT')

    <div class="mb-2">
        <label>Name</label>
        <input type="text" name="name"
               value="{{ $book->name }}"
               class="form-control">
    </div>

    <div class="mb-2">
        <label>Detail</label>
        <textarea name="detail"
                  class="form-control">{{ $book->detail }}</textarea>
    </div>

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('books.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection
```
# Step 10 : Now run this server and paste this url
```php
php artisan serve 
http://127.0.0.1:8000/books
```

<img width="1621" height="441" alt="image" src="https://github.com/user-attachments/assets/707bde6b-f4b0-4022-a7a5-33b2d7c014a5" />
<img width="1394" height="936" alt="image" src="https://github.com/user-attachments/assets/108bd16e-7b7f-4279-95dd-3eddf38a9db8" />


 

 
