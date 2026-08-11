@extends('books.layout')

@section('content')

<h3>Edit Tag</h3>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('tags.update',$tag->_id) }}">
    @csrf
    @method('PUT')

    <div class="mb-2">
        <label>Name</label>
        <input type="text" name="name"
               value="{{ old('name', $tag->name) }}"
               class="form-control" required>
    </div>

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('tags.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection
