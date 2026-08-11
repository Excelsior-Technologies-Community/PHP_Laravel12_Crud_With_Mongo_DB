@extends('books.layout')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Admin - Books Management</h3>
    <div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm me-2">Dashboard</a>
        <a class="btn btn-success btn-sm" href="{{ route('books.create') }}">Add New Book</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>Detail</th>
        <th>Status</th>
        <th>Category</th>
        <th>Author</th>
        <th width="200">Action</th>
    </tr>

    @foreach($books as $book)
    <tr>
        <td>{{ $loop->iteration + ($books->currentPage() - 1) * $books->perPage() }}</td>
        <td>{{ $book->name }}</td>
        <td>{{ Str::limit($book->detail, 50) }}</td>
        <td>
            <span class="badge bg-{{ $book->status === 'available' ? 'success' : ($book->status === 'borrowed' ? 'warning' : 'info') }}">
                {{ ucfirst($book->status) }}
            </span>
        </td>
        <td>{{ $book->category?->name ?? 'N/A' }}</td>
        <td>{{ $book->user?->name ?? 'N/A' }}</td>
        <td>
            <a class="btn btn-info btn-sm" href="{{ route('books.show',$book->_id) }}">Show</a>
            <a class="btn btn-primary btn-sm" href="{{ route('books.edit',$book->_id) }}">Edit</a>
            <form action="{{ route('books.destroy',$book->_id) }}" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

{{ $books->links() }}

@endsection
