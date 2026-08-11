@extends('books.layout')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Categories</h3>
    <a class="btn btn-success" href="{{ route('categories.create') }}">Add Category</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>Slug</th>
        <th>Description</th>
        <th width="150">Action</th>
    </tr>

    @foreach($categories as $category)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $category->name }}</td>
        <td>{{ $category->slug }}</td>
        <td>{{ $category->description }}</td>
        <td>
            <a class="btn btn-primary btn-sm" href="{{ route('categories.edit',$category->_id) }}">Edit</a>
            <form action="{{ route('categories.destroy',$category->_id) }}" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

@endsection
