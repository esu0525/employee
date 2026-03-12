@extends('layouts.app')

@section('title', 'History - Transfer')

@section('content')
<div class="page-content">
    <div class="page-header">
        <h1 class="page-title">History</h1>
        <p class="page-subtitle">View employee history records by category</p>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="search-container">
            <i data-lucide="search" class="search-icon"></i>
            <form method="GET" action="{{ route('employees.history-transfer') }}" id="searchForm">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search transferred employees..."
                    value="{{ $search }}"
                    onchange="document.getElementById('searchForm').submit()"
                >
            </form>
        </div>
        <div class="stat-meta" style="background: white; border: 1px solid var(--border); padding: 0.75rem 1.5rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
            <i data-lucide="arrow-right-left" style="color: var(--info);"></i>
            <span style="font-weight: 700; color: var(--text-main);">{{ $employees->count() }}</span>
            <span style="color: var(--text-muted); font-size: 0.875rem;">Total Transferred</span>
        </div>
    </div>

    <div class="table-container">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Date Joined</th>
                        <th>Transfer Date</th>
                        <th>Transfer Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($employees->count() > 0)
                        @foreach ($employees as $employee)
                        <tr>
                            <td class="employee-id">{{ $employee->id }}</td>
                            <td>
                                <a href="{{ route('employees.show', ['id' => $employee->id]) }}" class="employee-name-link">
                                    {{ $employee->name }}
                                </a>
                            </td>
                            <td>{{ $employee->position }}</td>
                            <td>
                                <span class="badge badge-outline badge-outline-blue">
                                    {{ $employee->department }}
                                </span>
                            </td>
                            <td>{{ $employee->date_joined ? $employee->date_joined->format('M d, Y') : '-' }}</td>
                            <td>{{ $employee->status_date ? $employee->status_date->format('M d, Y') : '-' }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i data-lucide="map-pin" style="width: 14px; height: 14px; color: var(--info);"></i>
                                    <span style="font-weight: 500;">
                                        {{ $employee->transfer_location ?: '-' }}
                                    </span>
                                </div>
                            </td>
                            <td><span class="badge badge-transfer">Transferred</span></td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i data-lucide="arrow-right-left" class="empty-icon" style="width: 48px; height: 48px;"></i>
                                    <p class="empty-title">No transferred employees found</p>
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
