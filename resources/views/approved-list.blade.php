@extends('layouts.app')

@section('title', 'Approved List')

@section('content')
<div class="page-content">
    <div class="page-header" style="margin-bottom: 1rem;">
        <h1 class="page-title">Approved List</h1>
        <p class="page-subtitle">View all approved document requests</p>
    </div>

    <!-- Action Bar -->
    <div class="action-bar" style="margin-bottom: 1rem;">
        <div class="search-container">
            <i data-lucide="search" class="search-icon"></i>
            <form method="GET" action="{{ route('employees.approved-list') }}" id="searchForm">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search approved requests..."
                    value="{{ $search }}"
                    onchange="document.getElementById('searchForm').submit()"
                >
            </form>
        </div>
        <div class="button-group">
            <div class="total-active-badge">
                <div class="total-active-icon" style="background: var(--success);">
                    <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="total-active-info">
                    <span class="total-active-count">{{ $approved_requests->count() }}</span>
                    <span class="total-active-label">Approved</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container" style="margin-top: 0;">
        <div class="table-wrapper">
            <table class="compact-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee / Agency</th>
                        <th>Document / Purpose</th>
                        <th>Copies</th>
                        <th>Request Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($approved_requests->count() > 0)
                        @foreach ($approved_requests as $req)
                        <tr>
                            <td class="employee-id">{{ $req->id }}</td>
                            <td>
                                <div style="line-height: 1.4;">
                                    <span style="font-weight: 600; color: var(--text-main); font-size: 0.8125rem;">{{ $req->employee_name }}</span>
                                    <br>
                                    <span style="font-size: 0.6875rem; color: var(--text-muted);">{{ $req->agency ?? $req->employee_id }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="display: block; font-weight: 600; font-size: 0.75rem; color: var(--primary);">{{ $req->request_type }}</span>
                                <span style="font-size: 0.6875rem; color: var(--text-muted);">Purpose: {{ $req->purpose ?? 'N/A' }}</span>
                            </td>
                            <td style="text-align: center; font-weight: 700; font-size: 0.8125rem;">{{ $req->num_copies ?? 1 }}</td>
                            <td style="white-space: nowrap; font-size: 0.8125rem;">{{ $req->request_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge badge-approved" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i>
                                    Approved
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i data-lucide="check-circle" class="empty-icon" style="width: 48px; height: 48px; color: var(--success);"></i>
                                    <p class="empty-title">No approved requests yet</p>
                                    <p class="empty-subtitle">Approved requests will appear here</p>
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
