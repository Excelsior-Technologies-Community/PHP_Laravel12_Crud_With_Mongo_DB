@extends('books.layout')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h3>Books List</h3>
    <a class="btn btn-success" href="{{ route('books.create') }}">Add New Book</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>Detail</th>
        <th width="250">Action</th>
    </tr>

    @foreach($books as $book)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $book->name }}</td>
        <td>{{ $book->detail }}</td>
        <td>
            <a class="btn btn-info btn-sm" href="{{ route('books.show',$book->_id) }}">Show</a>
            <a class="btn btn-primary btn-sm" href="{{ route('books.edit',$book->_id) }}">Edit</a>

            <form action="{{ route('books.destroy',$book->_id) }}"
                  method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm"
                        onclick="return confirm('Delete?')">
                    Delete
                </button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

@endsection