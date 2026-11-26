@extends('layouts.portal')

@section('title', 'User Administration | Masaganang Pagpapala Pasalubong')

@section('content')
<style>
    @font-face {
        font-family: 'modern-bold';
        src: url('/fonts/samsungsharpsans-bold.otf') format('opentype');
        font-display: swap;
    }

    @font-face {
        font-family: 'Recoleta-Bold';
        src: url('/fonts/Recoleta Bold.otf') format('opentype');
        font-display: swap;
    }

    .portal-content-wrapper,
    .portal-main {
        background: #FFFFFF !important;
        font-family: 'modern-bold', sans-serif;
    }

    .users-dash-wrap {
        width: 100%;
        padding: 4px 15px 60px 15px;
        box-sizing: border-box;
    }

    /* Header matching User Dashboard */
    .users-header-area {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .users-greeting-sub {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(18px, 2.2vw, 24px);
        color: #310669;
        line-height: 1;
        margin-bottom: 4px;
    }

    .users-display-title {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(34px, 5.5vw, 64px);
        color: #8a38f5;
        margin: 0;
        line-height: 1;
    }

    .pill-btn-outline {
        background: #FFFFFF;
        color: #310669 !important;
        border: 2.5px solid #E5E7EB;
        font-family: 'modern-bold', sans-serif;
        font-size: 13px;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 16px;
        text-transform: uppercase;
        transition: border-color 0.15s ease, background 0.15s ease;
    }

    .pill-btn-outline:hover {
        border-color: #310669;
        background: #F9FAFB;
    }

    /* 5 Role Stat Cards */
    .users-stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin-bottom: 18px;
    }

    .users-stat-box {
        background: #FFFFFF;
        border: 2.5px solid #E5E7EB;
        border-radius: 20px;
        padding: 16px 18px;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.12s ease, border-color 0.15s ease;
    }

    .users-stat-box:hover {
        transform: translateY(-2px);
        border-color: #8a38f5;
    }

    .users-stat-box.accent-purple { border-color: #D8B4FE; background: #FAF5FF; }
    .users-stat-box.accent-green  { border-color: #A7F3D0; background: #ECFDF5; }
    .users-stat-box.accent-blue   { border-color: #BFDBFE; background: #EFF6FF; }
    .users-stat-box.accent-amber  { border-color: #FDE68A; background: #FFFBEB; }
    .users-stat-box.accent-pink   { border-color: #FBCFE8; background: #FDF2F8; }

    .users-stat-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6B7280;
    }

    .users-stat-value {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(24px, 2.6vw, 34px);
        color: #310669;
        line-height: 1.1;
        margin-top: 6px;
    }

    /* Main Card */
    .dash-card {
        background: #FFFFFF;
        border: 2.5px solid #E5E7EB;
        border-radius: 20px;
        padding: clamp(18px, 2.5vw, 26px);
        box-shadow: none !important;
        box-sizing: border-box;
    }

    /* Search & Sort Toolbar */
    .users-toolbar {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        padding-bottom: 16px;
        border-bottom: 1.5px dashed #E5E7EB;
    }

    .role-filter-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .role-tab-btn {
        background: #F9FAFB;
        border: 2px solid #E5E7EB;
        color: #6B7280;
        font-family: 'modern-bold', sans-serif;
        font-size: 12px;
        padding: 8px 14px;
        border-radius: 12px;
        text-decoration: none;
        text-transform: uppercase;
        transition: all 0.15s ease;
    }

    .role-tab-btn:hover {
        border-color: #8a38f5;
        color: #310669;
        background: #FAF5FF;
    }

    .role-tab-btn.active {
        background: #8a38f5;
        border-color: #8a38f5;
        color: #FFFFFF;
    }

    .users-controls-group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        flex: 1;
        justify-content: flex-end;
    }

    .candy-search-wrap {
        position: relative;
        min-width: 240px;
        flex: 1;
        max-width: 340px;
    }

    .candy-search-input {
        width: 100%;
        padding: 10px 14px 10px 38px;
        border: 2.5px solid #E5E7EB;
        border-radius: 14px;
        font-family: 'modern-bold', sans-serif;
        font-size: 13px;
        color: #1F2937;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.15s ease;
    }

    .candy-search-input:focus {
        border-color: #8a38f5;
    }

    .candy-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        pointer-events: none;
    }

    .candy-select {
        padding: 10px 14px;
        border: 2.5px solid #E5E7EB;
        border-radius: 14px;
        font-family: 'modern-bold', sans-serif;
        font-size: 13px;
        color: #310669;
        background: #FFFFFF;
        outline: none;
        cursor: pointer;
    }

    .candy-select:focus {
        border-color: #8a38f5;
    }

    .pill-btn-purple-sm {
        background: #8a38f5;
        color: #FFFFFF;
        font-family: 'modern-bold', sans-serif;
        font-size: 12px;
        padding: 10px 18px;
        border-radius: 14px;
        text-transform: uppercase;
        border: none;
        box-shadow: 0px 4px 0px #560db6;
        cursor: pointer;
        transition: transform 0.1s ease, box-shadow 0.1s ease;
    }

    .pill-btn-purple-sm:hover {
        transform: translateY(1px);
        box-shadow: 0px 3px 0px #560db6;
    }

    .pill-btn-green-sm {
        background: #10B981;
        color: #FFFFFF;
        font-family: 'modern-bold', sans-serif;
        font-size: 12px;
        padding: 9px 16px;
        border-radius: 12px;
        text-transform: uppercase;
        border: none;
        box-shadow: 0px 4px 0px #059669;
        cursor: pointer;
        transition: transform 0.1s ease, box-shadow 0.1s ease;
    }

    .pill-btn-green-sm:hover {
        transform: translateY(1px);
        box-shadow: 0px 3px 0px #059669;
    }

    /* Users Table */
    .users-table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table thead th {
        padding: 12px 14px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6B7280;
        border-bottom: 2px solid #F3F4F6;
        text-align: left;
        white-space: nowrap;
    }

    .users-table tbody td {
        padding: 14px;
        border-bottom: 1.5px solid #F3F4F6;
        font-size: 13px;
        color: #1F2937;
        vertical-align: middle;
    }

    .users-table tbody tr:hover {
        background: #FAF5FF;
    }

    .user-cell-flex {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-mini-avatar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #E9D5FF;
        background: #FAF5FF;
        flex-shrink: 0;
    }

    /* Role Status Chips */
    .role-chip {
        display: inline-block;
        padding: 5px 11px;
        border-radius: 999px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .role-chip.role-admin     { background: #F3E8FF; color: #6B21A8; }
    .role-chip.role-developer { background: #DBEAFE; color: #1E40AF; }
    .role-chip.role-staff     { background: #FEF3C7; color: #92400E; }
    .role-chip.role-customer  { background: #DEF7EC; color: #03543F; }

    .sort-header-link {
        color: #6B7280;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .sort-header-link:hover {
        color: #8a38f5;
    }

    @media (max-width: 1100px) {
        .users-stats-grid { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 700px) {
        .users-stats-grid { grid-template-columns: repeat(2, 1fr); }
        .users-controls-group { justify-content: stretch; }
        .candy-search-wrap { max-width: none; }
    }
</style>

@php
    $activeRole = $filters['role'] ?? request('role', 'all');
    $activeSort = $filters['sort'] ?? request('sort', 'name_asc');
    $searchTerm = $filters['q'] ?? request('q', '');
    $counts = $roleCounts ?? [
        'all'       => $users->total(),
        'customer'  => 0,
        'staff'     => 0,
        'developer' => 0,
        'admin'     => 0,
    ];
@endphp

<div class="users-dash-wrap">
    <!-- 1. HEADER -->
    <div class="users-header-area">
        <div>
            <div class="users-greeting-sub">Access Control</div>
            <h1 class="users-display-title">User Administration</h1>
        </div>
        <a class="pill-btn-outline" href="{{ route('admin.dashboard') }}">&larr; Back to Dashboard</a>
    </div>

    @if(session('success'))
        <div style="background: #DEF7EC; border: 2px solid #31C48D; border-radius: 16px; padding: 12px 18px; color: #03543F; font-size: 13px; margin-bottom: 16px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #FEE2E2; border: 2px solid #F87171; border-radius: 16px; padding: 12px 18px; color: #991B1B; font-size: 13px; margin-bottom: 16px;">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- 2. ROLE COUNT STAT CARDS -->
    <div class="users-stats-grid">
        <a href="{{ route('admin.users.index', ['role' => 'all', 'q' => $searchTerm, 'sort' => $activeSort]) }}" class="users-stat-box accent-purple">
            <div class="users-stat-label">All Accounts</div>
            <div class="users-stat-value">{{ number_format($counts['all']) }}</div>
        </a>
        <a href="{{ route('admin.users.index', ['role' => 'customer', 'q' => $searchTerm, 'sort' => $activeSort]) }}" class="users-stat-box accent-green">
            <div class="users-stat-label">Customers</div>
            <div class="users-stat-value">{{ number_format($counts['customer']) }}</div>
        </a>
        <a href="{{ route('admin.users.index', ['role' => 'staff', 'q' => $searchTerm, 'sort' => $activeSort]) }}" class="users-stat-box accent-amber">
            <div class="users-stat-label">Staff</div>
            <div class="users-stat-value">{{ number_format($counts['staff']) }}</div>
        </a>
        <a href="{{ route('admin.users.index', ['role' => 'developer', 'q' => $searchTerm, 'sort' => $activeSort]) }}" class="users-stat-box accent-blue">
            <div class="users-stat-label">Developers</div>
            <div class="users-stat-value">{{ number_format($counts['developer']) }}</div>
        </a>
        <a href="{{ route('admin.users.index', ['role' => 'admin', 'q' => $searchTerm, 'sort' => $activeSort]) }}" class="users-stat-box accent-pink">
            <div class="users-stat-label">Admins</div>
            <div class="users-stat-value">{{ number_format($counts['admin']) }}</div>
        </a>
    </div>

    <!-- 3. MAIN USERS DIRECTORY CARD -->
    <div class="dash-card">
        <!-- Search, Role Filter Tabs & Sort Controls -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="users-toolbar" id="usersFilterForm">
            <input type="hidden" name="role" value="{{ $activeRole }}">

            <div class="role-filter-pills">
                @foreach(['all' => 'All', 'customer' => 'Customers', 'staff' => 'Staff', 'developer' => 'Developers', 'admin' => 'Admins'] as $roleKey => $roleLabel)
                    <a href="{{ route('admin.users.index', ['role' => $roleKey, 'q' => $searchTerm, 'sort' => $activeSort]) }}"
                       class="role-tab-btn {{ $activeRole === $roleKey ? 'active' : '' }}">
                        {{ $roleLabel }}
                    </a>
                @endforeach
            </div>

            <div class="users-controls-group">
                <div class="candy-search-wrap">
                    <svg class="candy-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text"
                           name="q"
                           id="liveUserSearchInput"
                           class="candy-search-input"
                           value="{{ $searchTerm }}"
                           placeholder="Search name or email..."
                           autocomplete="off">
                </div>

                <select name="sort" class="candy-select" onchange="this.form.submit()" aria-label="Sort users">
                    <option value="name_asc" @selected($activeSort === 'name_asc')>Sort: Name (A &rarr; Z)</option>
                    <option value="name_desc" @selected($activeSort === 'name_desc')>Sort: Name (Z &rarr; A)</option>
                    <option value="role_asc" @selected($activeSort === 'role_asc')>Sort: Role (A &rarr; Z)</option>
                    <option value="role_desc" @selected($activeSort === 'role_desc')>Sort: Role (Z &rarr; A)</option>
                    <option value="newest" @selected($activeSort === 'newest')>Sort: Newest Accounts</option>
                </select>

                <button type="submit" class="pill-btn-purple-sm">Filter</button>
            </div>
        </form>

        <!-- Users Table -->
        <div style="overflow-x: auto;">
            <table class="users-table" id="usersDirectoryTable">
                <thead>
                    <tr>
                        <th>
                            <a class="sort-header-link" href="{{ route('admin.users.index', ['role' => $activeRole, 'q' => $searchTerm, 'sort' => $activeSort === 'name_asc' ? 'name_desc' : 'name_asc']) }}">
                                User {!! $activeSort === 'name_asc' ? '&uarr;' : ($activeSort === 'name_desc' ? '&darr;' : '') !!}
                            </a>
                        </th>
                        <th>Email</th>
                        <th>
                            <a class="sort-header-link" href="{{ route('admin.users.index', ['role' => $activeRole, 'q' => $searchTerm, 'sort' => $activeSort === 'role_asc' ? 'role_desc' : 'role_asc']) }}">
                                Current Role {!! $activeSort === 'role_asc' ? '&uarr;' : ($activeSort === 'role_desc' ? '&darr;' : '') !!}
                            </a>
                        </th>
                        <th>Change Role</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr class="user-directory-row"
                        data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . $user->role) }}">
                        <td>
                            <div class="user-cell-flex">
                                <img src="{{ !empty($user->avatar) ? asset($user->avatar) : asset('images/default-avatar.jpeg') }}"
                                     alt="{{ $user->name }}"
                                     class="user-mini-avatar">
                                <div>
                                    <div style="color: #310669; font-size: 14px;">{{ $user->name }}</div>
                                    <div style="font-size: 11px; color: #6B7280;">ID #{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="color: #4B5563;">{{ $user->email }}</td>
                        <td>
                            <span class="role-chip role-{{ $user->role }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.role', $user) }}" style="display: flex; align-items: center; gap: 8px;">
                                @csrf @method('PUT')
                                <select class="candy-select" style="padding: 7px 12px; font-size: 12px;" name="role" aria-label="Role for {{ $user->name }}">
                                    @foreach(['customer', 'staff', 'developer', 'admin'] as $role)
                                        <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="pill-btn-green-sm">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #6B7280; padding: 32px;">
                            No users matched your search or role filter.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">{{ $users->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    // Instant client-side filtering on the currently visible page while typing
    const searchInput = document.getElementById('liveUserSearchInput');
    const rows = document.querySelectorAll('#usersDirectoryTable .user-directory-row');

    if (searchInput && rows.length) {
        searchInput.addEventListener('input', () => {
            const term = searchInput.value.trim().toLowerCase();
            rows.forEach(row => {
                const hay = row.dataset.search || '';
                row.style.display = !term || hay.includes(term) ? '' : 'none';
            });
        });
    }
})();
</script>
@endpush
