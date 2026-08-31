@extends('books.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3>My Borrowing History</h3>
        <p class="text-muted mb-0">
            Books you have borrowed and returned.
        </p>
    </div>

    <a
        href="{{ route('books.index') }}"
        class="btn btn-secondary"
    >
        Back to Books
    </a>
</div>

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

<div class="card">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Book</th>
                        <th>Borrowed At</th>
                        <th>Returned At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($borrowings as $borrowing)

                    <tr>

                        <td>
                            {{
                                $loop->iteration
                                + ($borrowings->currentPage() - 1)
                                * $borrowings->perPage()
                            }}
                        </td>

                        <td>
                            @if($borrowing->book)
                                <strong>
                                    {{ $borrowing->book->name }}
                                </strong>
                            @else
                                <span class="text-muted">
                                    Book deleted
                                </span>
                            @endif
                        </td>

                        <td>
                            {{
                                $borrowing->borrowed_at
                                    ? $borrowing->borrowed_at
                                        ->format('Y-m-d H:i')
                                    : 'N/A'
                            }}
                        </td>

                        <td>
                            {{
                                $borrowing->returned_at
                                    ? $borrowing->returned_at
                                        ->format('Y-m-d H:i')
                                    : '-'
                            }}
                        </td>

                        <td>

                            @if($borrowing->status === 'borrowed')

                                <span class="badge bg-warning text-dark">
                                    Borrowed
                                </span>

                            @else

                                <span class="badge bg-success">
                                    Returned
                                </span>

                            @endif

                        </td>

                        <td>

                            @if($borrowing->status === 'borrowed')

                                <form
                                    action="{{
                                        route(
                                            'borrowings.return',
                                            $borrowing->_id
                                        )
                                    }}"
                                    method="POST"
                                >
                                    @csrf

                                    <button
                                        class="btn btn-success btn-sm"
                                        onclick="return confirm('Return this book?')"
                                    >
                                        Return Book
                                    </button>
                                </form>

                            @else

                                <span class="text-muted">
                                    Completed
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="text-center py-4">
                            No borrowing history found.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        {{ $borrowings->links() }}

    </div>

</div>

@endsection