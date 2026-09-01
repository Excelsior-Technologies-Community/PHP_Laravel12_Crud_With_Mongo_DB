@extends('books.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>
        <h3 class="mb-1">
            Books List
        </h3>

        <small class="text-muted">
            Manage your MongoDB books
        </small>
    </div>

    <div>

        <a
            class="btn btn-success"
            href="{{ route('books.create') }}">
            Add New Book
        </a>

        <a
            class="btn btn-outline-secondary"
            href="{{ route('admin.dashboard') }}">
            Admin Dashboard
        </a>

    </div>

</div>


{{-- =========================================================
     ALERTS
========================================================= --}}

@if(session('success'))

<div class="alert alert-success">
    {{ session('success') }}
</div>

@endif


@if(session('error'))

<div class="alert alert-danger">
    {{ session('error') }}
</div>

@endif


{{-- =========================================================
     FILTERS
========================================================= --}}

<div class="card mb-3">

    <div class="card-header">
        <strong>
            Search & Advanced Filters
        </strong>
    </div>

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('books.index') }}">

            <div class="row g-2">

                {{-- Search --}}

                <div class="col-md-3">

                    <label class="form-label">
                        Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search books..."
                        value="{{ request('search') }}">

                </div>


                {{-- Status --}}

                <div class="col-md-2">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select">

                        <option value="">
                            All Status
                        </option>

                        <option
                            value="available"
                            {{ request('status') === 'available' ? 'selected' : '' }}>
                            Available
                        </option>

                        <option
                            value="borrowed"
                            {{ request('status') === 'borrowed' ? 'selected' : '' }}>
                            Borrowed
                        </option>

                        <option
                            value="sold"
                            {{ request('status') === 'sold' ? 'selected' : '' }}>
                            Sold
                        </option>

                    </select>

                </div>


                {{-- Category --}}

                <div class="col-md-2">

                    <label class="form-label">
                        Category
                    </label>

                    <select
                        name="category_id"
                        class="form-select">

                        <option value="">
                            All Categories
                        </option>

                        @foreach($categories as $category)

                        <option
                            value="{{ $category->_id }}"
                            {{ request('category_id') == $category->_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>

                        @endforeach

                    </select>

                </div>


                {{-- Tag --}}

                <div class="col-md-2">

                    <label class="form-label">
                        Tag
                    </label>

                    <select
                        name="tag_id"
                        class="form-select">

                        <option value="">
                            All Tags
                        </option>

                        @foreach($tags as $tag)

                        <option
                            value="{{ $tag->_id }}"
                            {{ request('tag_id') == $tag->_id ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>

                        @endforeach

                    </select>

                </div>


                {{-- Sorting --}}

                <div class="col-md-3">

                    <label class="form-label">
                        Sort By
                    </label>

                    <select
                        name="sort"
                        class="form-select">

                        <option
                            value="newest"
                            {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>
                            Newest First
                        </option>

                        <option
                            value="oldest"
                            {{ request('sort') === 'oldest' ? 'selected' : '' }}>
                            Oldest First
                        </option>

                        <option
                            value="name_asc"
                            {{ request('sort') === 'name_asc' ? 'selected' : '' }}>
                            Name A-Z
                        </option>

                        <option
                            value="name_desc"
                            {{ request('sort') === 'name_desc' ? 'selected' : '' }}>
                            Name Z-A
                        </option>

                        <option
                            value="status"
                            {{ request('sort') === 'status' ? 'selected' : '' }}>
                            Status
                        </option>

                    </select>

                </div>


                {{-- Date From --}}

                <div class="col-md-3">

                    <label class="form-label">
                        Created From
                    </label>

                    <input
                        type="date"
                        name="date_from"
                        class="form-control"
                        value="{{ request('date_from') }}">

                </div>


                {{-- Date To --}}

                <div class="col-md-3">

                    <label class="form-label">
                        Created To
                    </label>

                    <input
                        type="date"
                        name="date_to"
                        class="form-control"
                        value="{{ request('date_to') }}">

                </div>


                {{-- Buttons --}}

                <div class="col-md-3 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-primary me-2">
                        Filter
                    </button>

                    <a
                        href="{{ route('books.index') }}"
                        class="btn btn-secondary">
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- =========================================================
     ACTION BUTTONS
========================================================= --}}

<div class="mb-3">

    <a
        href="{{ route('books.export.csv') }}"
        class="btn btn-success btn-sm">
        Export CSV
    </a>

    <a
        href="{{ route('books.export.pdf') }}"
        class="btn btn-danger btn-sm">
        Export PDF
    </a>

    <a
        href="{{ route('books.index', ['trashed' => 1]) }}"
        class="btn btn-warning btn-sm">
        View Trashed
    </a>

</div>


{{-- =========================================================
     BULK DELETE FORM
========================================================= --}}

@if(!request('trashed'))

<form
    action="{{ route('books.bulk-delete') }}"
    method="POST"
    id="bulkDeleteForm">

    @csrf


    <div class="d-flex justify-content-between align-items-center mb-2">

        <div>

            <button
                type="submit"
                class="btn btn-danger btn-sm"
                onclick="return confirmBulkDelete()">
                Delete Selected
            </button>

        </div>

        <div class="text-muted small">

            Select books to delete

        </div>

    </div>


    @endif


    {{-- =========================================================
     BOOK TABLE
========================================================= --}}

    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            @if(!request('trashed'))

                            <th width="40">

                                <input
                                    type="checkbox"
                                    id="selectAll">

                            </th>

                            @endif

                            <th>No</th>

                            <th>Image</th>

                            <th>Name</th>

                            <th>Detail</th>

                            <th>Status</th>

                            <th>Category</th>

                            <th>Author</th>

                            <th>Created At</th>

                            <th width="280">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($books as $book)

                        <tr>

                            @if(!request('trashed'))

                            <td>

                                @if($book->status !== 'borrowed')

                                <input
                                    type="checkbox"
                                    name="book_ids[]"
                                    value="{{ $book->_id }}"
                                    class="book-checkbox">

                                @else

                                <span
                                    class="text-muted"
                                    title="Borrowed books cannot be bulk deleted">
                                    -
                                </span>

                                @endif

                            </td>

                            @endif


                            <td>

                                {{
                                $loop->iteration
                                + (
                                    ($books->currentPage() - 1)
                                    * $books->perPage()
                                )
                            }}

                            </td>


                            <td>

                                @if($book->image)

                                <img
                                    src="{{ asset('storage/' . $book->image) }}"
                                    alt="Book"
                                    width="50"
                                    height="50"
                                    style="object-fit:cover;border-radius:6px;">

                                @else

                                <span class="text-muted">
                                    No image
                                </span>

                                @endif

                            </td>


                            <td>

                                <strong>
                                    {{ $book->name }}
                                </strong>

                            </td>


                            <td>

                                {{ Str::limit($book->detail, 50) }}

                            </td>


                            <td>

                                @if($book->status === 'available')

                                <span class="badge bg-success">
                                    Available
                                </span>

                                @elseif($book->status === 'borrowed')

                                <span class="badge bg-warning text-dark">
                                    Borrowed
                                </span>

                                @else

                                <span class="badge bg-info">
                                    Sold
                                </span>

                                @endif

                            </td>


                            <td>

                                {{ $book->category?->name ?? 'N/A' }}

                            </td>


                            <td>

                                {{ $book->user?->name ?? 'N/A' }}

                            </td>


                            <td>

                                {{ $book->created_at?->format('Y-m-d') ?? '-' }}

                            </td>


                            <td>

                                @if(request('trashed'))

                                <form
                                    action="{{ route('books.restore', $book->_id) }}"
                                    method="POST"
                                    style="display:inline">

                                    @csrf

                                    <button
                                        class="btn btn-success btn-sm">
                                        Restore
                                    </button>

                                </form>


                                <form
                                    action="{{ route('books.force-delete', $book->_id) }}"
                                    method="POST"
                                    style="display:inline">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Permanently delete this book?')">
                                        Force Delete
                                    </button>

                                </form>

                                @else

                                <a
                                    class="btn btn-info btn-sm"
                                    href="{{ route('books.show', $book->_id) }}">
                                    Show
                                </a>


                                <a
                                    class="btn btn-primary btn-sm"
                                    href="{{ route('books.edit', $book->_id) }}">
                                    Edit
                                </a>


                                @auth

                                @if($book->status === 'available')

                                <form
                                    action="{{ route('books.borrow', $book->_id) }}"
                                    method="POST"
                                    style="display:inline">

                                    @csrf

                                    <button
                                        class="btn btn-success btn-sm">
                                        Borrow
                                    </button>

                                </form>

                                @elseif($book->status === 'borrowed')

                                <span class="badge bg-warning text-dark">
                                    Borrowed
                                </span>

                                @endif

                                @endauth


                                <form
                                    action="{{ route('books.destroy', $book->_id) }}"
                                    method="POST"
                                    style="display:inline">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this book?')">
                                        Delete
                                    </button>

                                </form>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td
                                colspan="{{ request('trashed') ? 9 : 10 }}"
                                class="text-center py-4">

                                No books found.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination - Numbers Only --}}
            <div class="d-flex justify-content-center mt-4">
                <ul class="pagination mb-0">

                    @for ($page = 1; $page <= $books->lastPage(); $page++)
                        <li class="page-item {{ $page == $books->currentPage() ? 'active' : '' }}">
                            <a
                                class="page-link"
                                href="{{ $books->appends(request()->query())->url($page) }}">
                                {{ $page }}
                            </a>
                        </li>
                        @endfor

                </ul>
            </div>

        </div>

    </div>


    @if(!request('trashed'))

</form>

@endif


{{-- =========================================================
     JAVASCRIPT
========================================================= --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const selectAll =
            document.getElementById('selectAll');

        const checkboxes =
            document.querySelectorAll('.book-checkbox');

        if (selectAll) {

            selectAll.addEventListener(
                'change',
                function() {

                    checkboxes.forEach(
                        checkbox => {
                            checkbox.checked =
                                selectAll.checked;
                        }
                    );

                }
            );

        }

    });


    function confirmBulkDelete() {
        const selected =
            document.querySelectorAll(
                '.book-checkbox:checked'
            );

        if (selected.length === 0) {

            alert(
                'Please select at least one book.'
            );

            return false;
        }

        return confirm(
            'Are you sure you want to delete ' +
            selected.length +
            ' selected book(s)?'
        );
    }
</script>

@endsection