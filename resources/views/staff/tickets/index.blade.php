@extends('layouts.staff')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Order Ticket Review & Dashboard</h4>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('staff.dashboard') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="In Production" {{ request('status') == 'In Production' ? 'selected' : '' }}>In Production</option>
                <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <div class="col-md-4">
            <select name="branch" class="form-select" onchange="this.form.submit()">
                <option value="">All Branches</option>
                <option value="Main Branch" {{ request('branch') == 'Main Branch' ? 'selected' : '' }}>Main Branch</option>
                <option value="San Jose Branch" {{ request('branch') == 'San Jose Branch' ? 'selected' : '' }}>San Jose Branch</option>
            </select>
        </div>
    </form>

    <!-- Tickets Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ticket #</th>
                            <th>Customer</th>
                            <th>Branch</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td><span class="fw-bold">{{ $ticket->ticket_number ?? '#'.$ticket->id }}</span></td>
                                <td>{{ $ticket->user->name ?? 'Walk-in Customer' }}</td>
                                <td>{{ $ticket->pickup_branch ?? 'Main Branch' }}</td>
                                <td><span class="badge bg-primary">{{ $ticket->status }}</span></td>
                                <td>₱{{ number_format($ticket->total_amount, 2) }}</td>
                                <td>{{ $ticket->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No order tickets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $tickets->links() }}
    </div>
</div>
@endsection