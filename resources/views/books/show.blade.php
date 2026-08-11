@extends('books.layout')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Book Details</h3>
    <div>
        <a class="btn btn-primary btn-sm" href="{{ route('books.edit',$book->_id) }}">Edit</a>
        <a class="btn btn-secondary btn-sm" href="{{ route('books.index') }}">Back</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                @if($book->image)
                    <img src="{{ asset('storage/' . $book->image) }}" class="img-fluid rounded" alt="Book">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 300px;">
                        <span class="text-muted">No Image</span>
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <h2>{{ $book->name }}</h2>
                <p><strong>Status:</strong>
                    <span class="badge bg-{{ $book->status === 'available' ? 'success' : ($book->status === 'borrowed' ? 'warning' : 'info') }}">
                        {{ ucfirst($book->status) }}
                    </span>
                </p>
                <p><strong>Category:</strong> {{ $book->category?->name ?? 'N/A' }}</p>
                <p><strong>Author:</strong> {{ $book->user?->name ?? 'N/A' }}</p>
                <p><strong>Tags:</strong>
                    @if($book->tags->count() > 0)
                        @foreach($book->tags as $tag)
                            <span class="badge bg-secondary">{{ $tag->name }}</span>
                        @endforeach
                    @else
                        N/A
                    @endif
                </p>
                <hr>
                <p><strong>Detail:</strong></p>
                <p>{{ $book->detail }}</p>
                <p class="text-muted"><small>Created: {{ $book->created_at?->format('Y-m-d H:i') }} | Updated: {{ $book->updated_at?->format('Y-m-d H:i') }}</small></p>
            </div>
        </div>
    </div>
</div>

@endsection
