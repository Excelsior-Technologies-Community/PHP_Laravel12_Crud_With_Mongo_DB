@extends('books.layout')

@section('content')

<h3>Edit Category</h3>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('categories.update',$category->_id) }}">
    @csrf
    @method('PUT')

    <div class="mb-2">
        <label>Name</label>
        <input type="text" name="name"
               value="{{ old('name', $category->name) }}"
               class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Description</label>
        <textarea name="description"
                  class="form-control">{{ old('description', $category->description) }}</textarea>
    </div>

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection
