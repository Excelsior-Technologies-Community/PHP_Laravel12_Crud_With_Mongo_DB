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

<form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="mb-2">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>

    <div class="mb-2">
        <label>Detail</label>
        <textarea name="detail" class="form-control">{{ old('detail') }}</textarea>
    </div>

    <div class="mb-2">
        <label>Status</label>
        <select name="status" class="form-select" required>
            <option value="">Select Status</option>
            <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
            <option value="borrowed" {{ old('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
            <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Sold</option>
        </select>
    </div>

    <div class="mb-2">
        <label>Category</label>
        <select name="category_id" class="form-select">
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->_id }}" {{ old('category_id') == $category->_id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-2">
        <label>Tags</label>
        <select name="tag_ids[]" class="form-select" multiple>
            @foreach($tags as $tag)
                <option value="{{ $tag->_id }}" {{ in_array($tag->_id, old('tag_ids', [])) ? 'selected' : '' }}>{{ $tag->name }}</option>
            @endforeach
        </select>
        <small class="text-muted">Hold Ctrl to select multiple</small>
    </div>

    <div class="mb-2">
        <label>Image</label>
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>

    <button class="btn btn-primary">Save</button>
    <a href="{{ route('books.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection
