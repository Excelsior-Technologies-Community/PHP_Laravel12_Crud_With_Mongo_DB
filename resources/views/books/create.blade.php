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