@extends('books.layout')

@section('content')

<h3>Add Tag</h3>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('tags.store') }}">
    @csrf

    <div class="mb-2">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>

    <button class="btn btn-primary">Save</button>
    <a href="{{ route('tags.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection
