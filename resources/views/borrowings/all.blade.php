@extends('books.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h3>All Borrowing Records</h3>
        <p class="text-muted mb-0">
            Complete borrowing history.
        </p>
    </div>

    <div>
        <a
            href="{{ route('admin.analytics') }}"
            class="btn btn-primary btn-sm"
        >
            Analytics
        </a>

        <a
            href="{{ route('admin.dashboard') }}"
            class="btn btn-secondary btn-sm"
        >
            Dashboard
        </a>
    </div>

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
                        <th>User</th>
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
                            {{ $borrowing->book?->name ?? 'Deleted Book' }}
                        </td>

                        <td>
                            {{ $borrowing->user?->name ?? 'Deleted User' }}
                        </td>

                        <td>
                            {{
                                $borrowing->borrowed_at
                                    ? $borrowing->borrowed_at
                                        ->format('Y-m-d H:i')
                                    : '-'
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
                                        Mark Returned
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
                        <td colspan="7" class="text-center py-4">
                            No borrowing records found.
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