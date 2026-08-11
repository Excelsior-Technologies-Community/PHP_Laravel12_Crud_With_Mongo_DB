@extends('books.layout')

@section('content')

<h3>Add Category</h3>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('categories.store') }}">
    @csrf

    <div class="mb-2">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>

    <div class="mb-2">
        <label>Description</label>
        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
    </div>

    <button class="btn btn-primary">Save</button>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection
