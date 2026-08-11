<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Admin Dashboard</h1>
        <div>
            <span class="me-3">Welcome, {{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Books</h5>
                    <p class="card-text display-4">{{ $totalBooks }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Available</h5>
                    <p class="card-text display-4">{{ $availableBooks }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Borrowed</h5>
                    <p class="card-text display-4">{{ $borrowedBooks }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Sold</h5>
                    <p class="card-text display-4">{{ $soldBooks }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Total Users</div>
                <div class="card-body">
                    <h2>{{ $totalUsers }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recent Books</span>
                    <a href="{{ route('admin.books.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Name</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($recentBooks as $book)
                            <tr>
                                <td>{{ $book->name }}</td>
                                <td><span class="badge bg-{{ $book->status === 'available' ? 'success' : ($book->status === 'borrowed' ? 'warning' : 'info') }}">{{ ucfirst($book->status) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recent Users</span>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Name</th><th>Email</th></tr></thead>
                        <tbody>
                            @foreach($recentUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Quick Actions</div>
                <div class="card-body">
                    <a href="{{ route('books.create') }}" class="btn btn-success mb-2">Add New Book</a><br>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary mb-2">Add Category</a><br>
                    <a href="{{ route('tags.create') }}" class="btn btn-info mb-2">Add Tag</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
