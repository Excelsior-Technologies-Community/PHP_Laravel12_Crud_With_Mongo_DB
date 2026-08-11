<!DOCTYPE html>
<html>
<head>
    <title>Books Export - PDF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; }
        table { font-size: 11px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Books List</h1>
        <p class="text-center">Generated on: {{ date('Y-m-d H:i') }}</p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Detail</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($books as $book)
                <tr>
                    <td>{{ $book->_id ?? $book->id }}</td>
                    <td>{{ $book->name }}</td>
                    <td>{{ $book->detail }}</td>
                    <td>{{ ucfirst($book->status) }}</td>
                    <td>{{ $book->category?->name ?? 'N/A' }}</td>
                    <td>{{ $book->user?->name ?? 'N/A' }}</td>
                    <td>{{ $book->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
