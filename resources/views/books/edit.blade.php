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

<form method="POST" action="{{ route('books.update',$book->_id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-2">
        <label>Name</label>
        <input type="text" name="name"
               value="{{ old('name', $book->name) }}"
               class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Detail</label>
        <textarea name="detail"
                  class="form-control">{{ old('detail', $book->detail) }}</textarea>
    </div>

    <div class="mb-2">
        <label>Status</label>
        <select name="status" class="form-select" required>
            <option value="">Select Status</option>
            <option value="available" {{ old('status', $book->status) == 'available' ? 'selected' : '' }}>Available</option>
            <option value="borrowed" {{ old('status', $book->status) == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
            <option value="sold" {{ old('status', $book->status) == 'sold' ? 'selected' : '' }}>Sold</option>
        </select>
    </div>

    <div class="mb-2">
        <label>Category</label>
        <select name="category_id" class="form-select">
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->_id }}" {{ old('category_id', $book->category_id) == $category->_id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-2">
        <label>Tags</label>
        <select name="tag_ids[]" class="form-select" multiple>
            @foreach($tags as $tag)
                <option value="{{ $tag->_id }}" {{ in_array($tag->_id, old('tag_ids', $book->tag_ids ?? [])) ? 'selected' : '' }}>{{ $tag->name }}</option>
            @endforeach
        </select>
        <small class="text-muted">Hold Ctrl to select multiple</small>
    </div>

    <div class="mb-2">
        <label>Current Image</label><br>
        @if($book->image)
            <img src="{{ asset('storage/' . $book->image) }}" alt="Book" width="100" height="100" style="object-fit: cover;">
        @else
            <span class="text-muted">No image</span>
        @endif
    </div>

    <div class="mb-2">
        <label>Change Image</label>
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('books.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection
