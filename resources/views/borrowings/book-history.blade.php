@extends('books.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">


<div>

    <h3>
        Borrowing History
    </h3>

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

{{-- =========================================================
BOOK BORROWING HISTORY
========================================================= --}}

<div class="card">

<div class="card-body">

    <h5 class="mb-3">
        {{ $book->name }}
    </h5>


    <div class="table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">

                <tr>

                    <th>No</th>

                    <th>User</th>

                    <th>Borrowed At</th>

                    <th>Due Date</th>

                    <th>Returned At</th>

                    <th>Status</th>

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


                    {{-- User --}}

                    <td>

                        {{
                            $borrowing->user?->name
                            ?? 'Unknown User'
                        }}

                    </td>


                    {{-- Borrowed At --}}

                    <td>

                        {{
                            $borrowing->borrowed_at
                                ? $borrowing->borrowed_at->format('Y-m-d H:i')
                                : '-'
                        }}

                    </td>


                    {{-- Due Date --}}

                    <td>

                        @if($borrowing->due_at)

                            {{ $borrowing->due_at->format('Y-m-d') }}

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

                </tr>

            @empty

                <tr>

                    <td
                        colspan="6"
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
