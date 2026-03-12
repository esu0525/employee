@extends('layouts.app')

@section('title', 'History - Inactive')

@section('content')
<div class="page-content">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">History</h1>
        <p class="page-subtitle">View employee history records by category</p>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="search-container">
            <i data-lucide="search" class="search-icon"></i>
            <form method="GET" action="{{ route('employees.history-inactive') }}" id="searchForm">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search inactive employees..."
                    value="{{ $search }}"
                    onchange="document.getElementById('searchForm').submit()"
                >
            </form>
        </div>
        <div class="stat-meta" style="background: white; border: 1px solid var(--border); padding: 0.75rem 1.5rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
            <i data-lucide="user-x" style="color: var(--text-muted);"></i>
            <span style="font-weight: 700; color: var(--text-main);">{{ $employees->count() }}</span>
            <span style="color: var(--text-muted); font-size: 0.875rem;">Total Inactive</span>
        </div>
    </div>

    <!-- Table -->
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
                        <th>Inactive Since</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($employees->count() > 0)
                        @foreach ($employees as $employee)
                        <tr>
                            <td class="employee-id" style="color: #6b7280;">{{ $employee->id }}</td>
                            <td style="font-weight: 500;">
                                <a href="{{ route('employees.show', ['id' => $employee->id]) }}" class="employee-name-link">
                                    {{ $employee->name }}
                                </a>
                            </td>
                            <td>{{ $employee->position }}</td>
                            <td>
                                <span class="badge badge-outline badge-outline-gray">
                                    {{ $employee->department }}
                                </span>
                            </td>
                            <td>{{ $employee->date_joined ? $employee->date_joined->format('m/d/Y') : '-' }}</td>
                            <td>
                                {{ $employee->status_date ? $employee->status_date->format('m/d/Y') : '-' }}
                            </td>
                            <td>
                                <span class="badge badge-inactive">Inactive</span>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i data-lucide="user-x" class="empty-icon" style="width: 48px; height: 48px;"></i>
                                    <p class="empty-title">No inactive employees found</p>
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
