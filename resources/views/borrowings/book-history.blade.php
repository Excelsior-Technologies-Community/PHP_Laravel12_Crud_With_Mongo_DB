@extends('books.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h3>Borrowing History</h3>
        <p class="text-muted mb-0">
            {{ $book->name }}
        </p>
    </div>

    <a
        href="{{ route('books.show', $book->_id) }}"
        class="btn btn-secondary"
    >
        Back
    </a>

</div>

<div class="card">

    <div class="card-body">

        <h5 class="mb-3">
            {{ $book->name }}
        </h5>

        <div class="table-responsive">

            <table class="table table-bordered">

                <thead class="table-dark">

                    <tr>
                        <th>No</th>
                        <th>User</th>
                        <th>Borrowed At</th>
                        <th>Returned At</th>
                        <th>Status</th>
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
                            {{ $borrowing->user?->name ?? 'Unknown User' }}
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

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="text-center">
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