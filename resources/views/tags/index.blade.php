@extends('books.layout')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Tags</h3>
    <a class="btn btn-success" href="{{ route('tags.create') }}">Add Tag</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>Slug</th>
        <th width="150">Action</th>
    </tr>

    @foreach($tags as $tag)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $tag->name }}</td>
        <td>{{ $tag->slug }}</td>
        <td>
            <a class="btn btn-primary btn-sm" href="{{ route('tags.edit',$tag->_id) }}">Edit</a>
            <form action="{{ route('tags.destroy',$tag->_id) }}" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

@endsection
