@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-content">
    <!-- Hero Header -->
    <div class="dashboard-hero" style="margin-bottom: 2rem;">
        @php
            $currentUser = \App\Models\User::find(session('auth_user_id'));
            $isAdmin = $currentUser && $currentUser->role === 'admin';
        @endphp
        <div class="hero-content">
            <h1 class="hero-title">{{ $isAdmin ? 'Overview Dashboard' : 'Employee Portal' }}</h1>
            <p class="hero-subtitle light-theme-text-fix">{{ $isAdmin ? 'Welcome to the 201 System command center.' : 'Hello, ' . ($currentUser->name ?? 'User') . '! Welcome back to your workspace.' }}</p>
        </div>
        <div class="hero-date">
            <div class="date-box">
                <i data-lucide="calendar"></i>
                <span>{{ now()->format('F d, Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    @if($isAdmin)
    <div class="summary-cards-grid">
        <div class="summary-card card-primary animate-up" style="--delay: 0.1s;">
            <div class="card-icon-box">
                <i data-lucide="users"></i>
            </div>
            <div class="card-stats">
                <span class="stats-value">{{ $total_active }}</span>
                <span class="stats-label">Active Employees</span>
            </div>
        </div>
        


        <div class="summary-card card-warning animate-up" style="--delay: 0.3s;">
            <div class="card-icon-box">
                <i data-lucide="file-clock"></i>
            </div>
            <div class="card-stats">
                <span class="stats-value">{{ $pending_requests }}</span>
                <span class="stats-label">Pending Requests</span>
            </div>
        </div>

        <div class="summary-card card-danger animate-up" style="--delay: 0.4s;">
            <div class="card-icon-box">
                <i data-lucide="archive"></i>
            </div>
            <div class="card-stats">
                <span class="stats-value">{{ $history_count }}</span>
                <span class="stats-label">Archive Records</span>
            </div>
        </div>
    </div>
    @else
    <div class="summary-cards-grid">
        <div class="summary-card card-primary animate-up" style="--delay: 0.1s;">
            <div class="card-icon-box">
                <i data-lucide="users"></i>
            </div>
            <div class="card-stats">
                <span class="stats-value">{{ $total_active }}</span>
                <span class="stats-label">Active Records</span>
            </div>
        </div>

        <div class="summary-card card-info animate-up" style="--delay: 0.2s;">
            <div class="card-icon-box">
                <i data-lucide="file-check-2"></i>
            </div>
            <div class="card-stats">
                <span class="stats-value">{{ $my_pending_requests }}</span>
                <span class="stats-label">My Pending Requests</span>
            </div>
        </div>

        <div class="summary-card card-warning animate-up" style="--delay: 0.3s;">
            <div class="card-icon-box">
                <i data-lucide="contact"></i>
            </div>
            <div class="card-stats">
                <span class="stats-value">ACTIVE</span>
                <span class="stats-label">Account Status</span>
            </div>
        </div>
    </div>
    @endif

    @php
        $hasRequestsAccess = $currentUser && $currentUser->hasPermission('view_requests');
    @endphp

    <!-- Main Layout Grid -->
    <div class="dashboard-grid-main">
        <!-- Row 1: Recent Requests & System Activity -->
        <div class="dashboard-grid-row">
            @if($hasRequestsAccess)
            <!-- Recent Requests -->
            <div class="dashboard-section animate-up" style="--delay: 0.6s;">
                <div class="section-header">
                    <h2 class="section-title">Recent Requests</h2>
                    <a href="{{ route('employees.requests') }}" class="btn-text">View All <i data-lucide="arrow-right"></i></a>
                </div>
                <div class="recent-list">
                    @forelse($recent_requests as $request)
                        <div class="recent-item">
                            <div class="item-avatar-small">
                                {{ strtoupper(substr($request->employee_name, 0, 1)) }}
                            </div>
                            <div class="item-info">
                                <span class="item-name">{{ $request->employee_name }}</span>
                                <span class="item-sub">{{ $request->request_type }}</span>
                            </div>
                            <div class="item-meta">
                                <span class="badge badge-pending">{{ ucfirst($request->status) }}</span>
                                <span class="item-date">{{ \Carbon\Carbon::parse($request->request_date)->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-mini">
                            <p>No recent requests</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @endif

            @if($isAdmin)
            <!-- Recent Activity (Status Changes) -->
            <div class="dashboard-section animate-up" style="--delay: 0.8s;">
                <div class="section-header">
                    <h2 class="section-title">System Activity</h2>
                </div>
                <div class="activity-timeline-wrapper" style="max-height: 380px; overflow-y: auto; padding-right: 10px; scrollbar-width: thin; scrollbar-color: var(--primary) transparent;">
                    <div class="activity-timeline">
                        @forelse($recent_activity as $act)
                            <div class="timeline-item">
                                <div class="timeline-point point-{{ $act->status }}"></div>
                                <div class="timeline-content">
                                    <p class="activity-text"><strong>{{ $act->name }}</strong> was updated to <span class="text-{{ $act->status }}">{{ $act->status }}</span></p>
                                    <span class="activity-time">{{ \Carbon\Carbon::parse($act->status_date)->format('M d, Y | h:i A') }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="empty-mini">
                                <p>No recent activity</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Row 2: Quick Actions & My Tasks -->
        <div class="dashboard-grid-row">
            <!-- Quick Actions -->
            <div class="dashboard-section animate-up" style="--delay: 0.7s;">
                <div class="section-header">
                    <h2 class="section-title">Quick Actions</h2>
                </div>
                @php
                    $canAdd   = $currentUser && $currentUser->hasPermission('edit_masterlist');
                    $canArchi = $currentUser && $currentUser->hasPermission('view_archive');
                    $canAcc   = $currentUser && $currentUser->hasPermission('manage_accounts');
                @endphp
                <div class="quick-actions-grid">
                    @if($canAdd)
                    <a href="{{ route('employees.add') }}" class="action-btn">
                        <i data-lucide="user-plus"></i>
                        <span>Add New</span>
                    </a>
                    @endif
                    <a href="{{ route('portal.index') }}" class="action-btn">
                        <i data-lucide="file-plus"></i>
                        <span>File Request</span>
                    </a>
                    @if($canArchi)
                    <a href="{{ route('employees.archive') }}" class="action-btn">
                        <i data-lucide="archive"></i>
                        <span>Archive List</span>
                    </a>
                    @endif
                    @if($canAcc)
                    <a href="{{ route('admin.users.index') }}" class="action-btn">
                        <i data-lucide="shield"></i>
                        <span>Accounts</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- My Tasks -->
            <div class="dashboard-section animate-up" style="--delay: 0.9s;" id="taskSection" data-user-id="{{ session('auth_user_id') }}">
                <div class="section-header">
                    <h2 class="section-title">My Tasks</h2>
                    <span class="badge badge-info" id="taskCount" style="display:none;">0 Tasks</span>
                </div>
                
                <!-- Task List Container -->
                <div id="taskList" style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 0.5rem; max-height: 240px; overflow-y: auto; padding-right: 5px; scrollbar-width: thin; scrollbar-color: var(--primary) transparent;">
                    <!-- Tasks will be injected here -->
                </div>

                <!-- Horizontal Empty State -->
                <div id="allCaughtUp" class="empty-mini" style="background: rgba(var(--primary-rgb), 0.05); display: flex; align-items: center; gap: 1.25rem; padding: 1.5rem; border-radius: 18px;">
                    <i data-lucide="check-circle" style="width: 38px; height: 38px; color: var(--primary); flex-shrink: 0;"></i>
                    <div style="text-align: left;">
                        <p style="font-weight: 700; margin-bottom: 2px;">You're all caught up!</p>
                        <p style="font-size: 0.8rem; opacity: 0.7;">Keep up the great work.</p>
                    </div>
                </div>

                <!-- Add Task Input -->
                <div class="task-input-box" style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                    <input type="text" id="taskInput" class="form-input" placeholder="Add a quick task (e.g. Upload file)" style="flex: 1; padding: 0.75rem 1rem; font-size: 0.9rem;">
                    <button id="addTaskBtn" class="btn-primary" style="padding: 0 1.25rem; border-radius: 12px;">
                        <i data-lucide="plus" style="width: 20px;"></i>
                    </button>
                </div>
            </div>


        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Premium Dashboard Styles */
    .dashboard-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-main) 100%);
        padding: 2.5rem;
        border-radius: 24px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
    }

    .hero-title {
        font-family: 'Outfit', sans-serif;
        font-size: 2.75rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        line-height: 1.2;
        background: linear-gradient(135deg, #1e1b4b 0%, #4f46e5 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: inline-block;
    }

    body[data-theme="dark"] .hero-title {
        background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-subtitle {
        color: var(--text-muted);
        font-size: 1.1rem;
        font-weight: 500;
    }

    /* Light Theme Subtitle Fix */
    body[data-theme="light"] .light-theme-text-fix {
        color: #1e1b4b; /* Deep blue for better contrast in light mode */
        opacity: 0.9;
    }

    .date-box {
        background: var(--primary-soft);
        color: var(--primary);
        padding: 0.75rem 1.25rem;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
    }

    /* Summary Cards */
    .summary-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .summary-card {
        background: var(--bg-card);
        padding: 1.75rem;
        border-radius: 24px;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: var(--shadow-sm);
    }

    .summary-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary);
    }

    .card-icon-box {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-icon-box i { width: 1.75rem; height: 1.75rem; }

    .card-primary .card-icon-box { background: rgba(79, 70, 229, 0.15); color: #4f46e5; }
    .card-info .card-icon-box { background: rgba(14, 165, 233, 0.15); color: #0ea5e9; }
    .card-warning .card-icon-box { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .card-danger .card-icon-box { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

    .card-stats { display: flex; flex-direction: column; }
    .stats-value { font-size: 1.75rem; font-weight: 800; color: var(--text-main); font-family: 'Outfit', sans-serif; line-height: 1.1; }
    .stats-label { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }

    /* Layout Grids - New 2-Column Row System */
    .dashboard-grid-main {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        margin-top: 1rem;
    }

    .dashboard-grid-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 2rem;
        align-items: stretch;
    }

    .dashboard-grid-row > .dashboard-section {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .dashboard-section {
        background: var(--bg-card);
        border-radius: 28px;
        border: 1px solid var(--border);
        padding: 2rem;
        box-shadow: var(--shadow-sm);
        flex: 1;
    }

    @media (max-width: 1200px) {
        .dashboard-grid-row {
            grid-template-columns: 1fr;
        }
    }


    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .section-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .btn-text {
        color: var(--primary);
        font-weight: 700;
        text-decoration: none;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: gap 0.3s;
    }

    .btn-text:hover { gap: 0.75rem; }

    /* Chart */
    .chart-container-premium { 
        height: 300px; 
        position: relative; 
        width: 100%;
        background: rgba(255, 255, 255, 0.02);
        border-radius: 18px;
        padding: 10px;
    }

    /* Recent List */
    .recent-list { display: flex; flex-direction: column; gap: 1rem; }
    .recent-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--bg-main);
        border-radius: 18px;
        transition: 0.3s;
        border: 1px solid transparent;
    }
    .recent-item:hover { border-color: var(--primary-soft); transform: scale(1.02); }

    .item-avatar-small {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 50%;
        background: var(--primary-gradient);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1rem;
        box-shadow: var(--shadow-sm);
    }

    .item-info { flex: 1; display: flex; flex-direction: column; }
    .item-name { font-weight: 700; color: var(--text-main); font-size: 0.95rem; }
    .item-sub { font-size: 0.8rem; color: var(--text-muted); }

    .item-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; }
    .item-date { font-size: 0.75rem; color: var(--text-muted); font-weight: 500; }

    /* Quick Actions */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .action-btn {
        background: var(--bg-main);
        border: 1px solid var(--border);
        padding: 1.25rem;
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: var(--text-main);
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.3s;
    }

    .action-btn:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
    }

    .action-btn i { width: 1.5rem; height: 1.5rem; }

    /* Timeline */
    .activity-timeline { display: flex; flex-direction: column; gap: 1.75rem; position: relative; padding-left: 1.5rem; padding-top: 0.5rem; }
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 4px;
        top: 10px;
        bottom: 5px;
        width: 2px;
        background: var(--border);
    }

    .timeline-item { display: flex; gap: 1.25rem; position: relative; }
    .timeline-point {
        width: 10px; height: 10px; border-radius: 50%; background: var(--border);
        position: absolute; left: -19px; top: 6px; z-index: 1;
        box-shadow: 0 0 0 4px var(--bg-card);
    }

    .point-resign { background: #ef4444; }
    .point-retired { background: #f59e0b; }
    .point-transfer { background: #0ea5e9; }
    .point-active { background: #10b981; }

    .activity-text { font-size: 0.9375rem; color: var(--text-main); margin-bottom: 2px; line-height: 1.5; font-weight: 500; }
    .activity-time { font-size: 0.8rem; color: var(--text-muted); font-weight: 600; opacity: 0.8; }

    .text-resign { color: #ef4444; font-weight: 700; }
    .text-retired { color: #f59e0b; font-weight: 700; }
    .text-transfer { color: #0ea5e9; font-weight: 700; }
    .text-active { color: #10b981; font-weight: 700; }

    /* Custom scrollbar for activity */
    .activity-timeline-wrapper::-webkit-scrollbar { width: 4px; }
    .activity-timeline-wrapper::-webkit-scrollbar-track { background: transparent; }
    .activity-timeline-wrapper::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; opacity: 0.3; }

    /* Animations */
    .animate-up {
        opacity: 0;
        transform: translateY(30px);
        animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        animation-delay: var(--delay);
    }

    @keyframes slideUp {
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1200px) {
        .dashboard-main-grid { grid-template-columns: 1fr; }
        .dashboard-column.narrow { flex-direction: row; }
        .dashboard-column.narrow > * { flex: 1; }
    }

    @media (max-width: 768px) {
        .dashboard-hero { flex-direction: column; text-align: center; gap: 1.5rem; padding: 2rem; }
        .dashboard-column.narrow { flex-direction: column; }
        .summary-cards-grid { grid-template-columns: 1fr 1fr; }
    }

    /* Task Scrollbar Fix */
    #taskList::-webkit-scrollbar { width: 4px; }
    #taskList::-webkit-scrollbar-track { background: transparent; }
    #taskList::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; opacity: 0.3; }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endpush
