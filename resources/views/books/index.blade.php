@extends('books.layout')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h3>Books List</h3>
    <div>
        <a class="btn btn-success" href="{{ route('books.create') }}">Add New Book</a>
        <a class="btn btn-outline-secondary" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('books.index') }}" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search books..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->_id }}" {{ request('category_id') == $category->_id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="tag_id" class="form-select">
                    <option value="">All Tags</option>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->_id }}" {{ request('tag_id') == $tag->_id ? 'selected' : '' }}>{{ $tag->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('books.index') }}" class="btn btn-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="mb-2">
    <a href="{{ route('books.export.csv') }}" class="btn btn-success btn-sm">Export CSV</a>
    <a href="{{ route('books.export.pdf') }}" class="btn btn-danger btn-sm">Export PDF</a>
    <a href="{{ route('books.index', ['trashed' => 1]) }}" class="btn btn-warning btn-sm">View Trashed</a>
</div>

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Image</th>
        <th>Name</th>
        <th>Detail</th>
        <th>Status</th>
        <th>Category</th>
        <th>Author</th>
        <th width="250">Action</th>
    </tr>

    @forelse($books as $book)
    <tr>
        <td>{{ $loop->iteration + ($books->currentPage() - 1) * $books->perPage() }}</td>
        <td>
            @if($book->image)
                <img src="{{ asset('storage/' . $book->image) }}" alt="Book" width="50" height="50" style="object-fit: cover;">
            @else
                <span class="text-muted">No image</span>
            @endif
        </td>
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
            @if(request('trashed'))
                <form action="{{ route('books.restore', $book->_id) }}" method="POST" style="display:inline">
                    @csrf
                    <button class="btn btn-success btn-sm">Restore</button>
                </form>
                <form action="{{ route('books.force-delete', $book->_id) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete?')">Force Delete</button>
                </form>
            @else
                <a class="btn btn-info btn-sm" href="{{ route('books.show',$book->_id) }}">Show</a>
                <a class="btn btn-primary btn-sm" href="{{ route('books.edit',$book->_id) }}">Edit</a>
                <form action="{{ route('books.destroy',$book->_id) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</button>
                </form>
            @endif
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="8" class="text-center">No books found.</td>
    </tr>
    @endforelse
</table>

{{ $books->links() }}

@endsection
