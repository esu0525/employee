@extends('layouts.app')

@section('title', 'Master List')

@section('content')
<div class="page-content">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Master List</h1>
        <p class="page-subtitle">View and manage all active employees</p>
    </div>



    <!-- Action Bar -->
    <div class="action-bar">
        <div class="search-container">
            <form method="GET" action="{{ route('employees.index') }}" id="searchForm" style="width: 100%; position: relative;">
                <i data-lucide="search" class="search-icon"></i>
                    <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search by name, position, or agency..."
                    value="{{ $search }}"
                    onchange="document.getElementById('searchForm').submit()"
                >
            </form>
        </div>
        <div class="button-group">
            <div class="total-active-badge">
                <div class="total-active-icon">
                    <i data-lucide="users" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="total-active-info">
                    <span class="total-active-count">{{ $total_active }}</span>
                    <span class="total-active-label">Total Active</span>
                </div>
            </div>
            <button class="btn btn-primary" onclick="openAddEmployeeModal()">
                <i data-lucide="plus"></i>
                Add Employee
            </button>
        </div>
    </div>



    <!-- Modern Card List -->
    <div class="master-list-grid">
        @if ($employees->count() > 0)
            @foreach ($employees as $employee)
            <div class="master-item-card" onclick="window.location='{{ route('employees.show', ['id' => $employee->id]) }}'">
                <div class="master-card-left">
                    <div class="master-avatar-wrapper">
                        <div class="master-avatar">
                            @if($employee->profile_picture)
                                <img src="{{ asset($employee->profile_picture) }}" alt="{{ $employee->name }}">
                            @else
                                {{ strtoupper(substr($employee->name, 0, 1)) }}{{ strtoupper(substr(strrchr($employee->name, " "), 1, 1)) }}
                            @endif
                        </div>
                    </div>
                    <div class="master-info">
                        @php
                            $displayName = $employee->name;
                            if ($employee->last_name && $employee->first_name) {
                                $displayName = $employee->last_name . ', ' . $employee->first_name;
                                if ($employee->middle_name) {
                                    $displayName .= ' ' . substr($employee->middle_name, 0, 1) . '.';
                                }
                            }
                        @endphp
                        <span class="master-name">{{ $displayName }}</span>
                        <span class="master-sub" style="font-size: 0.85rem; opacity: 0.8;">{{ $employee->position }} • {{ $employee->agency }}</span>
                    </div>
                </div>
                
                <div class="master-card-right">
                    <div class="master-card-meta" style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem;">
                        <span class="badge badge-active">Active</span>
                        <span style="font-size: 0.75rem; color: #64748b; font-weight: 500;">Joined {{ $employee->date_joined ? $employee->date_joined->format('M d, Y') : '-' }}</span>
                    </div>
                    <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); openUpdateStatusModal('{{ $employee->id }}', '{{ $employee->name }}')" style="padding: 0.5rem 1rem; font-size: 0.75rem; border-radius: 10px;">
                        <i data-lucide="refresh-cw" style="width: 14px; height: 14px; margin-right: 4px;"></i>
                        Status
                    </button>
                    <i data-lucide="chevron-right" class="master-action-arrow"></i>
                </div>
            </div>
            @endforeach
        @else
            <div class="empty-state">
                <i data-lucide="search" class="empty-icon" style="width: 48px; height: 48px;"></i>
                <p class="empty-title">No employees found matching your search criteria</p>
                <p class="empty-subtitle">Try adjusting your search terms</p>
            </div>
        @endif
    </div>
</div>

<!-- Success Toast Notification -->
@if(session('success_message'))
<div id="successToast" class="toast-notification toast-success">
    <div class="toast-icon">
        <i data-lucide="check-circle" style="width: 22px; height: 22px;"></i>
    </div>
    <div class="toast-content">
        <p class="toast-title">Success!</p>
        <p class="toast-text">{{ session('success_message') }}</p>
    </div>
    <button class="toast-close" onclick="closeToast()">
        <i data-lucide="x" style="width: 16px; height: 16px;"></i>
    </button>
</div>
@endif

<!-- Add Employee Modal -->
<div id="addEmployeeModal" class="modal">
    <div class="modal-content" style="max-width: 580px;">
        <form method="POST" action="{{ route('employees.store') }}" style="display: flex; flex-direction: column; max-height: 90vh; overflow: hidden;">
            @csrf
            <div class="modal-header">
                <h2 class="modal-title">Add New Employee</h2>
                <button type="button" class="icon-btn" onclick="closeAddEmployeeModal()" style="color: var(--text-muted);">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <div class="modal-body" style="flex: 1; overflow-y: auto; min-height: 0;">
                <div class="form-grid" style="gap: 0.875rem;">
                    <!-- Name Components -->
                    <div class="form-group">
                        <label class="form-label" for="last_name">Surname *</label>
                        <input type="text" id="last_name" name="last_name" class="form-input" placeholder="Dela Cruz" required oninput="syncFullName()">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" class="form-input" placeholder="Juan" required oninput="syncFullName()">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="middle_name">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name" class="form-input" placeholder="Abad" oninput="syncFullName()">
                    </div>

                    <!-- Hidden but kept for compatibility -->
                    <input type="hidden" id="name" name="name">

                    <!-- Current Position -->
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" for="position">Current Position *</label>
                        <input type="text" id="position" name="position" class="form-input" placeholder="Teacher I" required>
                    </div>

                    <!-- Birthday & Age -->
                    <div class="form-group">
                        <label class="form-label" for="date_of_birth">Birthday *</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="form-input" required onchange="calculateAge()">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="age">Age</label>
                        <input type="text" id="age" class="form-input" placeholder="Auto" readonly style="background: #f1f5f9; color: var(--text-muted);">
                    </div>

                    <!-- Sex -->
                    <div class="form-group">
                        <label class="form-label" for="sex">Sex *</label>
                        <select id="sex" name="sex" class="form-input" required>
                            <option value="" disabled selected>Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <!-- Agency/Department -->
                    <div class="form-group">
                        <label class="form-label" for="agency">Agency *</label>
                        <input type="text" id="agency" name="agency" class="form-input" placeholder="SDO - Caloocan City" required>
                    </div>

                    <!-- Address -->
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" for="address">Address *</label>
                        <input type="text" id="address" name="address" class="form-input" placeholder="123 Rizal Street, Brgy. Example, Caloocan City" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink: 0;">
                <button type="button" class="btn btn-outline" onclick="closeAddEmployeeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                    Save Employee
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="modal">
    <div class="modal-content" style="max-width: 420px;">
        <form id="updateStatusForm" method="POST" action="">
            @csrf
            <div class="modal-header">
                <h2 class="modal-title">Update Employee Status</h2>
                <button type="button" class="icon-btn" onclick="closeUpdateStatusModal()" style="color: var(--text-muted);">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <p id="statusEmployeeName" style="font-weight: 600; margin-bottom: 1rem; color: var(--text-main); font-size: 0.9375rem;"></p>
                <div class="form-grid" style="grid-template-columns: 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="status">New Status *</label>
                        <select id="statusSelect" name="status" class="form-input" required onchange="toggleTransferLocation()">
                            <option value="" disabled selected>Select status</option>
                            <option value="resign">Resign</option>
                            <option value="retired">Retire / Retired</option>
                            <option value="transfer">Transfer</option>
                            <option value="others">Others</option>
                        </select>
                    </div>

                    <div id="transferLocationGroup" class="form-group" style="display: none;">
                        <label class="form-label" for="transfer_location">Transfer Location *</label>
                        <input type="text" id="transfer_location" name="transfer_location" class="form-input" placeholder="Enter agency or office name">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeUpdateStatusModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Toast Notification */
    .toast-notification {
        position: fixed;
        top: 2rem;
        right: 2rem;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-radius: var(--radius-lg);
        box-shadow: 0 20px 60px -15px rgba(0, 0, 0, 0.25);
        animation: toastSlideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        max-width: 420px;
        backdrop-filter: blur(12px);
    }

    .toast-success {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 1px solid #6ee7b7;
        color: #065f46;
    }

    .toast-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        background: #10b981;
        color: white;
        border-radius: var(--radius-md);
        flex-shrink: 0;
    }

    .toast-content {
        flex: 1;
    }

    .toast-title {
        font-weight: 700;
        font-size: 0.9375rem;
        margin-bottom: 0.125rem;
    }

    .toast-text {
        font-size: 0.8125rem;
        color: #047857;
        line-height: 1.4;
    }

    .toast-close {
        background: none;
        border: none;
        color: #6ee7b7;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: var(--radius-sm);
        transition: var(--transition);
        flex-shrink: 0;
    }

    .toast-close:hover {
        background: rgba(0, 0, 0, 0.05);
        color: #065f46;
    }

    @keyframes toastSlideIn {
        from {
            opacity: 0;
            transform: translateX(100px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    @keyframes toastSlideOut {
        from {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
        to {
            opacity: 0;
            transform: translateX(100px) scale(0.95);
        }
    }

    /* Form select styling */
    .form-input select,
    select.form-input {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
        cursor: pointer;
    }

    /* Clickable Row Styles */
    .employee-row-clickable {
        transition: all 0.2s ease;
    }
    .employee-row-clickable:hover {
        background-color: #f8fafc !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .employee-name-link {
        transition: color 0.2s ease;
    }
    .employee-name-link:hover {
        color: var(--primary-color) !important;
    }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/masterlist.js') }}"></script>
@endpush
