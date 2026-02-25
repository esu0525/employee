@extends('layouts.app')

@section('title', 'History - Transfer')

@section('content')
<div class="page-content">
    <div class="page-header">
        <h1 class="page-title">History</h1>
        <p class="page-subtitle">View employee history records by category</p>
    </div>

    <div class="history-tabs">
        <div class="tabs-nav">
            <a href="{{ route('employees.history-inactive') }}" class="tab-link tab-inactive {{ Route::is('employees.history-inactive') ? 'active' : '' }}">
                <i data-lucide="file-x"></i>
                Inactive
            </a>
            <a href="{{ route('employees.history-resign') }}" class="tab-link tab-resign {{ Route::is('employees.history-resign') ? 'active' : '' }}">
                <i data-lucide="user-minus"></i>
                Resign
            </a>
            <a href="{{ route('employees.history-retired') }}" class="tab-link tab-retired {{ Route::is('employees.history-retired') ? 'active' : '' }}">
                <i data-lucide="user-x"></i>
                Retired
            </a>
            <a href="{{ route('employees.history-transfer') }}" class="tab-link tab-transfer {{ Route::is('employees.history-transfer') ? 'active' : '' }}">
                <i data-lucide="arrow-right-left"></i>
                Transfer
            </a>
        </div>
    </div>

    <div style="margin-bottom: 1.5rem;">
        <div class="search-container" style="max-width: 28rem;">
            <i data-lucide="search" class="search-icon"></i>
            <form method="GET" action="{{ route('employees.history-transfer') }}" id="searchForm">
                <input type="text" name="search" class="search-input" placeholder="Search transferred employees..." value="{{ $search }}" onchange="document.getElementById('searchForm').submit()">
            </form>
        </div>
    </div>

    <div style="margin-bottom: 1.5rem;">
        <div class="stat-card stat-card-blue" style="display: inline-block; width: auto; min-width: 300px;">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i data-lucide="arrow-right-left" style="width: 32px; height: 32px;"></i>
                </div>
                <div class="stat-card-info" style="margin-left: 1rem;">
                    <p class="stat-label">Total Transferred Employees</p>
                    <p class="stat-value">{{ count($employees) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr style="background: linear-gradient(to right, #eff6ff, #dbeafe);">
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
                        <tr style="transition: background 0.2s;" onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background=''">
                            <td class="employee-id" style="color: #2563eb;">{{ $employee->id }}</td>
                            <td style="font-weight: 500; color: #1f2937;">{{ $employee->name }}</td>
                            <td>{{ $employee->position }}</td>
                            <td>
                                <span class="badge badge-outline badge-outline-blue">
                                    {{ $employee->department }}
                                </span>
                            </td>
                            <td>{{ $employee->date_joined ? $employee->date_joined->format('m/d/Y') : '-' }}</td>
                            <td>{{ $employee->status_date ? $employee->status_date->format('m/d/Y') : '-' }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i data-lucide="map-pin" style="width: 16px; height: 16px; color: #3b82f6;"></i>
                                    <span style="color: #374151; font-weight: 500;">
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
