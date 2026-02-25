@extends('layouts.app')

@section('title', 'Request List')

@section('content')
<div class="page-content">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Request List</h1>
        <p class="page-subtitle">Manage and review employee requests</p>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="search-container">
            <i data-lucide="search" class="search-icon"></i>
            <form method="GET" action="{{ route('employees.requests') }}" id="searchForm">
                <input type="hidden" name="status" value="{{ $status_filter }}">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search requests by name, type, or ID..."
                    value="{{ $search }}"
                >
            </form>
        </div>
        <div class="button-group">
            <a href="{{ route('employees.requests', ['search' => $search, 'status' => 'all']) }}" 
               class="btn-filter filter-all {{ $status_filter === 'all' ? 'active' : '' }}">
                <i data-lucide="filter"></i>
                All
            </a>
            <a href="{{ route('employees.requests', ['search' => $search, 'status' => 'pending']) }}" 
               class="btn-filter filter-pending {{ $status_filter === 'pending' ? 'active' : '' }}">
                Pending
            </a>
            <a href="{{ route('employees.requests', ['search' => $search, 'status' => 'approved']) }}" 
               class="btn-filter filter-approved {{ $status_filter === 'approved' ? 'active' : '' }}">
                Approved
            </a>
            <a href="{{ route('employees.requests', ['search' => $search, 'status' => 'rejected']) }}" 
               class="btn-filter filter-rejected {{ $status_filter === 'rejected' ? 'active' : '' }}">
                Rejected
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="stat-card stat-card-indigo">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <p class="stat-label">Total Requests</p>
                    <p class="stat-value">{{ $all_requests }}</p>
                </div>
                <div class="stat-icon">
                    <i data-lucide="file-check" style="width: 28px; height: 28px;"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-orange">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <p class="stat-label">Pending</p>
                    <p class="stat-value">{{ $pending_count }}</p>
                </div>
                <div class="stat-icon">
                    <i data-lucide="clock" style="width: 28px; height: 28px;"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-green">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <p class="stat-label">Approved</p>
                    <p class="stat-value">{{ $approved_count }}</p>
                </div>
                <div class="stat-icon">
                    <i data-lucide="check-circle" style="width: 28px; height: 28px;"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-red">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <p class="stat-label">Rejected</p>
                    <p class="stat-value">{{ $rejected_count }}</p>
                </div>
                <div class="stat-icon">
                    <i data-lucide="x-circle" style="width: 28px; height: 28px;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Employee Name</th>
                        <th>Employee ID</th>
                        <th>Request Type</th>
                        <th>Request Date</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($requests->count() > 0)
                        @foreach ($requests as $request)
                        <tr>
                            <td class="employee-id">{{ $request->id }}</td>
                            <td class="employee-name-link" style="font-weight: 500;">
                                {{ $request->employee_name }}
                            </td>
                            <td>{{ $request->employee_id }}</td>
                            <td>
                                @php
                                $type_colors = [
                                    'leave' => 'badge-outline-blue',
                                    'transfer' => 'badge-outline-purple',
                                    'resignation' => 'badge-outline-red',
                                    'update' => 'badge-outline-green'
                                ];
                                $color_class = $type_colors[$request->request_type] ?? '';
                                @endphp
                                <span class="badge badge-outline {{ $color_class }}">
                                    {{ $request->request_type }}
                                </span>
                            </td>
                            <td>{{ $request->request_date->format('m/d/Y') }}</td>
                            <td style="max-width: 300px;">
                                <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $request->description }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $request->status }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                                <td>
                                    @if ($request->status === 'pending')
                                    <div style="display: flex; gap: 0.25rem;">
                                        <form action="{{ route('requests.approve', $request->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="icon-btn icon-btn-green" title="Approve">
                                                <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('requests.reject', $request->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="icon-btn icon-btn-red" title="Reject">
                                                <i data-lucide="x-circle" style="width: 18px; height: 18px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <span style="color: #9ca3af; font-size: 0.875rem;">-</span>
                                    @endif
                                </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i data-lucide="alert-circle" class="empty-icon" style="width: 48px; height: 48px;"></i>
                                    <p class="empty-title">No requests found matching your criteria</p>
                                    <p class="empty-subtitle">Try adjusting your filters or search terms</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
