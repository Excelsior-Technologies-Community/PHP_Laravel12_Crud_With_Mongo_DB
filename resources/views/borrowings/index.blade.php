@extends('books.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">


<div>
    <h3>
        My Borrowing History
    </h3>

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

{{-- =========================================================
SUCCESS MESSAGE
========================================================= --}}

@if(session('success'))


<div class="alert alert-success">
    {{ session('success') }}
</div>


@endif

{{-- =========================================================
ERROR MESSAGE
========================================================= --}}

@if(session('error'))


<div class="alert alert-danger">
    {{ session('error') }}
</div>


@endif

{{-- =========================================================
BORROWING TABLE
========================================================= --}}

<div class="card">


<div class="card-body">

    <div class="table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">

                <tr>

                    <th>No</th>

                    <th>Book</th>

                    <th>Borrowed At</th>

                    <th>Due Date</th>

                    <th>Returned At</th>

                    <th>Status</th>

                    <th>Action</th>

                </tr>

            </thead>


            <tbody>

            @forelse($borrowings as $borrowing)

                <tr>

                    {{-- No --}}

                    <td>
                        {{
                            $loop->iteration
                            + (
                                ($borrowings->currentPage() - 1)
                                * $borrowings->perPage()
                            )
                        }}
                    </td>


                    {{-- Book --}}

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


                    {{-- Borrowed At --}}

                    <td>

                        {{
                            $borrowing->borrowed_at
                                ? $borrowing->borrowed_at->format('Y-m-d H:i')
                                : 'N/A'
                        }}

                    </td>


                    {{-- Due Date --}}

                    <td>

                        @if($borrowing->due_at)

                            {{ $borrowing->due_at->format('Y-m-d') }}

                            @if($borrowing->status === 'borrowed')

                                @if($borrowing->isOverdue())

                                    <br>

                                    <span class="badge bg-danger mt-1">
                                        Overdue
                                    </span>

                                @else

                                    <br>

                                    <small class="text-muted">

                                        {{
                                            $borrowing->daysRemaining()
                                        }}

                                        day(s) remaining

                                    </small>

                                @endif

                            @endif

                        @else

                            -

                        @endif

                    </td>


                    {{-- Returned At --}}

                    <td>

                        {{
                            $borrowing->returned_at
                                ? $borrowing->returned_at->format('Y-m-d H:i')
                                : '-'
                        }}

                    </td>


                    {{-- Status --}}

                    <td>

                        @if($borrowing->status === 'borrowed')

                            @if($borrowing->isOverdue())

                                <span class="badge bg-danger">
                                    Overdue
                                </span>

                            @else

                                <span class="badge bg-warning text-dark">
                                    Borrowed
                                </span>

                            @endif

                        @else

                            <span class="badge bg-success">
                                Returned
                            </span>

                        @endif

                    </td>


                    {{-- Action --}}

                    <td>

                        @if($borrowing->status === 'borrowed')

                            <form
                                action="{{ route('borrowings.return', $borrowing->_id) }}"
                                method="POST"
                            >

                                @csrf

                                <button
                                    type="submit"
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

                    <td
                        colspan="7"
                        class="text-center py-4"
                    >
                        No borrowing history found.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    {{-- =====================================================
         PAGINATION - NUMBERS ONLY
    ====================================================== --}}

    @if($borrowings->hasPages())

        <div class="d-flex justify-content-center mt-4">

            <ul class="pagination mb-0">

                @for(
                    $page = 1;
                    $page <= $borrowings->lastPage();
                    $page++
                )

                    <li
                        class="page-item
                        {{ $page == $borrowings->currentPage() ? 'active' : '' }}"
                    >

                        <a
                            class="page-link"
                            href="{{ $borrowings->appends(request()->query())->url($page) }}"
                        >
                            {{ $page }}
                        </a>

                    </li>

                @endfor

            </ul>

        </div>

    @endif

</div>


</div>

@endsection
