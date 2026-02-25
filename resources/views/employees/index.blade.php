@extends('layouts.app')

@section('title', 'Master List - Employee Management System')

@section('content')
<div class="page-content">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Master List</h1>
        <p class="page-subtitle">View and manage all active employees</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        <i data-lucide="check-circle" class="alert-icon"></i>
        <span class="alert-text">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="search-container">
            <i data-lucide="search" class="search-icon"></i>
            <form method="GET" action="{{ route('employees.index') }}" id="searchForm">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search by name, position, department, or ID..."
                    value="{{ $search }}"
                    onchange="document.getElementById('searchForm').submit()"
                >
            </form>
        </div>
        <div class="button-group">
            <button class="btn btn-primary" onclick="openAddEmployeeModal()">
                <i data-lucide="plus"></i>
                Add Employee
            </button>
            <a href="#" class="btn btn-outline">
                <i data-lucide="download"></i>
                Export
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-card-blue">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <p class="stat-label">Total Active</p>
                    <p class="stat-value">{{ $total_active }}</p>
                    <div class="stat-meta">
                        <i data-lucide="trending-up" style="width: 14px; height: 14px;"></i>
                        <span>Employees</span>
                    </div>
                </div>
                <div class="stat-icon">
                    <i data-lucide="users" style="width: 32px; height: 32px;"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-purple">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <p class="stat-label">Departments</p>
                    <p class="stat-value">{{ $total_departments }}</p>
                    <div class="stat-meta">
                        <i data-lucide="building-2" style="width: 14px; height: 14px;"></i>
                        <span>Active Depts</span>
                    </div>
                </div>
                <div class="stat-icon">
                    <i data-lucide="building-2" style="width: 32px; height: 32px;"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-pink">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <p class="stat-label">Search Results</p>
                    <p class="stat-value">{{ $filtered_count }}</p>
                    <div class="stat-meta">
                        <i data-lucide="search" style="width: 14px; height: 14px;"></i>
                        <span>Matching</span>
                    </div>
                </div>
                <div class="stat-icon">
                    <i data-lucide="search" style="width: 32px; height: 32px;"></i>
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
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date Joined</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td class="employee-id">{{ $employee->id }}</td>
                        <td>
                            <a href="#" class="employee-name-link">
                                {{ $employee->name }}
                            </a>
                        </td>
                        <td>{{ $employee->position }}</td>
                        <td>
                            <span class="badge badge-outline badge-outline-indigo">
                                {{ $employee->department }}
                            </span>
                        </td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->phone }}</td>
                        <td>{{ $employee->date_joined->format('m/d/Y') }}</td>
                        <td>
                            <span class="badge badge-active">Active</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i data-lucide="search" class="empty-icon" style="width: 48px; height: 48px;"></i>
                                <p class="empty-title">No employees found matching your search criteria</p>
                                <p class="empty-subtitle">Try adjusting your search terms</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div id="addEmployeeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add New Employee</h2>
        </div>
        <form method="POST" action="#">
            @csrf
            <div class="modal-body">
                <!-- Basic Information -->
                <div class="form-section">
                    <h3 class="form-section-title">Basic Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="name">Full Name *</label>
                            <input type="text" id="name" name="name" class="form-input" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="position">Position *</label>
                            <input type="text" id="position" name="position" class="form-input" placeholder="Senior Developer" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="department">Department *</label>
                            <input type="text" id="department" name="department" class="form-input" placeholder="Engineering" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="date_joined">Date Joined *</label>
                            <input type="date" id="date_joined" name="date_joined" class="form-input" required>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="form-section">
                    <h3 class="form-section-title">Contact Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-input" placeholder="john.doe@company.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone *</label>
                            <input type="tel" id="phone" name="phone" class="form-input" placeholder="+1 (555) 123-4567" required>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label" for="address">Address</label>
                            <input type="text" id="address" name="address" class="form-input" placeholder="123 Main Street, City, State ZIP">
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="form-section">
                    <h3 class="form-section-title">Personal Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="date_of_birth">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="emergency_contact">Emergency Contact</label>
                            <input type="text" id="emergency_contact" name="emergency_contact" class="form-input" placeholder="Jane Doe">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="emergency_phone">Emergency Phone</label>
                            <input type="tel" id="emergency_phone" name="emergency_phone" class="form-input" placeholder="+1 (555) 987-6543">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeAddEmployeeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Employee</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openAddEmployeeModal() {
    document.getElementById('addEmployeeModal').classList.add('active');
}

function closeAddEmployeeModal() {
    document.getElementById('addEmployeeModal').classList.remove('active');
}

// Close modal on outside click
document.getElementById('addEmployeeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddEmployeeModal();
    }
});

// Reinitialize icons after content load
lucide.createIcons();
</script>
@endpush
