@extends('books.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2>MongoDB Book Analytics</h2>

        <p class="text-muted">
            Statistics generated using MongoDB aggregation.
        </p>
    </div>

    <div>

        <a
            href="{{ route('admin.dashboard') }}"
            class="btn btn-secondary"
        >
            Dashboard
        </a>

        <a
            href="{{ route('admin.borrowings.index') }}"
            class="btn btn-primary"
        >
            Borrowing Records
        </a>

    </div>

</div>


{{-- Book statistics --}}

<div class="row mb-4">

    <div class="col-md-3">

        <div class="card text-white bg-primary">

            <div class="card-body">

                <h6>Total Books</h6>

                <h2>
                    {{ $totalBooks }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card text-white bg-success">

            <div class="card-body">

                <h6>Available</h6>

                <h2>
                    {{ $availableBooks }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card text-white bg-warning">

            <div class="card-body">

                <h6>Borrowed</h6>

                <h2>
                    {{ $borrowedBooks }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card text-white bg-info">

            <div class="card-body">

                <h6>Sold</h6>

                <h2>
                    {{ $soldBooks }}
                </h2>

            </div>

        </div>

    </div>

</div>


{{-- Borrowing statistics --}}

<div class="row mb-4">

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Total Borrowings</h6>

                <h2>
                    {{ $totalBorrowings }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Currently Borrowed</h6>

                <h2 class="text-warning">
                    {{ $activeBorrowings }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <h6>Returned</h6>

                <h2 class="text-success">
                    {{ $returnedBorrowings }}
                </h2>

            </div>

        </div>

    </div>

</div>


<div class="row">


    {{-- Books by status --}}

    <div class="col-md-6 mb-4">

        <div class="card">

            <div class="card-header">
                <strong>Books by Status</strong>
            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($booksByStatus as $item)

                        <tr>

                            <td>
                                {{ ucfirst($item->_id ?? 'Unknown') }}
                            </td>

                            <td>
                                <strong>
                                    {{ $item->total }}
                                </strong>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="2" class="text-center">
                                No data available.
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- Books by category --}}

    <div class="col-md-6 mb-4">

        <div class="card">

            <div class="card-header">
                <strong>Books by Category</strong>
            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Total Books</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($booksByCategory as $item)

                        <tr>

                            <td>
                                {{ $item['name'] }}
                            </td>

                            <td>
                                <strong>
                                    {{ $item['total'] }}
                                </strong>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="2" class="text-center">
                                No category data available.
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- Most borrowed books --}}

    <div class="col-md-12 mb-4">

        <div class="card">

            <div class="card-header">

                <strong>
                    Most Borrowed Books
                </strong>

            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <thead>

                        <tr>
                            <th>Rank</th>
                            <th>Book</th>
                            <th>Total Times Borrowed</th>
                        </tr>

                    </thead>

                    <tbody>

                    @forelse($mostBorrowedBooks as $index => $item)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $item['name'] }}
                            </td>

                            <td>
                                <strong>
                                    {{ $item['total_borrowed'] }}
                                </strong>
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="3"
                                class="text-center"
                            >
                                No borrowing data available.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- Monthly borrowing statistics --}}

    <div class="col-md-12 mb-4">

        <div class="card">

            <div class="card-header">

                <strong>
                    Monthly Borrowing Statistics
                </strong>

            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <thead>

                        <tr>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Total Borrowings</th>
                        </tr>

                    </thead>

                    <tbody>

                    @forelse($monthlyBorrowings as $item)

                        <tr>

                            <td>
                                {{ $item->_id->year }}
                            </td>

                            <td>
                                {{ $item->_id->month }}
                            </td>

                            <td>
                                <strong>
                                    {{ $item->total }}
                                </strong>
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="3"
                                class="text-center"
                            >
                                No monthly borrowing data available.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection