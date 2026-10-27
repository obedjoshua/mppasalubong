@extends('layouts.portal')

@section('title', 'User administration')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">User administration</h1>
    <p class="text-secondary mb-0">Assign application roles. Changes are enforced server-side and affect portal access immediately.</p>
</div>
<div class="portal-module">
    <div class="table-responsive">
        <table class="table portal-table table-hover align-middle mb-0">
            <thead><tr><th>User</th><th>Email</th><th>Current role</th><th>Change role</th></tr></thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td><td>{{ $user->email }}</td><td><span class="badge text-bg-secondary">{{ $user->role }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.users.role', $user) }}" class="d-flex gap-2">
                            @csrf @method('PUT')
                            <select class="form-select form-select-sm" name="role" aria-label="Role for {{ $user->name }}">
                                @foreach(['customer', 'staff', 'developer', 'admin'] as $role)
                                    <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm btn-primary">Save</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $users->links() }}</div>
</div>
@endsection
