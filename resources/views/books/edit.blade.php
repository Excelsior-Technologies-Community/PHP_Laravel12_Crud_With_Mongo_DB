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