@extends('books.layout')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Admin - Users Management</h3>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">Dashboard</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Joined</th>
    </tr>

    @foreach($users as $user)
    <tr>
        <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td><span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'secondary' }}">{{ ucfirst($user->role) }}</span></td>
        <td>{{ $user->created_at?->format('Y-m-d') }}</td>
    </tr>
    @endforeach
</table>

{{ $users->links() }}

@endsection
