@extends('layouts.staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/staff/settings.css') }}">
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card settings-card p-4 border-0 shadow-sm">
            <h1 class="h4 fw-bold mb-1">Staff Terminal Settings</h1>
            <p class="text-muted small mb-4">View session configuration and hardware terminal status.</p>

            <div class="list-group list-group-flush">
                <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0">Authenticated User</h6>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>
                    <span class="badge bg-success">Active Session</span>
                </div>

                <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0">Camera Stream Integration</h6>
                        <small class="text-muted">HTML5 QR Code Video Engine</small>
                    </div>
                    <span class="badge bg-primary">Ready</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection