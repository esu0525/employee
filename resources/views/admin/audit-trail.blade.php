@extends('layouts.app')

@section('title', 'Audit Trail')

@section('content')
<div class="page-content animate-fade-in">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 class="page-title" style="font-family: 'Outfit', sans-serif; font-weight: 800; color: var(--text-main); font-size: 2rem; margin: 0;">Audit Trail</h1>
            <p class="page-subtitle" style="color: #475569; font-size: 1rem; margin-top: 0.25rem; font-weight: 500;">Monitor system activities and administrative logs</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card-premium" style="padding: 1.5rem; margin-bottom: 2rem; background: var(--bg-card); border-radius: 20px; border: 1px solid var(--border-light); box-shadow: var(--shadow-sm);">
        <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: flex-end;">
            <!-- Search Filter -->
            <div style="flex: 1; min-width: 300px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.75rem;">Search Logs</label>
                <div style="position: relative;">
                    <i data-lucide="search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 18px; color: var(--text-muted);"></i>
                    <input type="text" id="logSearch" placeholder="Search by user, action, or description..." 
                           oninput="filterLogs()"
                           style="width: 100%; padding: 0.85rem 1rem 0.85rem 3rem; border-radius: 12px; border: 1px solid var(--border-light); background: var(--bg-main); color: var(--text-main); font-size: 0.95rem; outline: none; transition: all 0.3s focus: border-color: var(--primary);">
                </div>
            </div>

            <!-- Year Filter -->
            <div style="width: 150px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.75rem;">Year</label>
                <select id="yearFilter" onchange="filterLogs()" style="width: 100%; padding: 0.85rem; border-radius: 12px; border: 1px solid var(--border-light); background: var(--bg-main); color: var(--text-main); outline: none;">
                    <option value="all">All Years</option>
                    @php $currentYear = date('Y'); @endphp
                    @for($y = $currentYear; $y >= 2024; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Month Filter -->
            <div style="width: 180px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.75rem;">Month</label>
                <select id="monthFilter" onchange="filterLogs()" style="width: 100%; padding: 0.85rem; border-radius: 12px; border: 1px solid var(--border-light); background: var(--bg-main); color: var(--text-main); outline: none;">
                    <option value="all">All Months</option>
                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $month)
                        <option value="{{ $index + 1 }}">{{ $month }}</option>
                    @endforeach
                </select>
            </div>
            
            <button onclick="resetFilters()" style="padding: 0.85rem 1.25rem; border-radius: 12px; border: 1px solid var(--border-light); background: white; color: var(--text-muted); font-weight: 700; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                Reset
            </button>

            <!-- Cleanup Logs Button -->
            <button onclick="openCleanupModal()" style="padding: 0.85rem 1.25rem; border-radius: 12px; border: none; background: #ef4444; color: white; font-weight: 700; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 0.5rem;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                <i data-lucide="trash-2" style="width: 18px;"></i>
                Cleanup Logs
            </button>
        </div>
    </div>

    <div id="cleanupModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 10000; align-items: center; justify-content: center;">
        <div class="animate-scale-up" style="background: white; width: 400px; border-radius: 20px; padding: 2rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.25rem; margin: 0;">Cleanup Audit Logs</h3>
                <button onclick="closeCleanupModal()" style="background: none; border: none; cursor: pointer; color: var(--text-muted);"><i data-lucide="x"></i></button>
            </div>
            
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem;">Select the period coverage to delete logs. This action <strong style="color: #ef4444;">cannot be undone</strong>.</p>
            
            <form action="{{ route('admin.audit-trail.cleanup') }}" method="POST" id="cleanupForm">
                @csrf
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Start Date</label>
                    <input type="date" name="start_date" id="cleanStart" required style="width: 100%; padding: 0.85rem; border-radius: 12px; border: 1px solid var(--border-light); background: var(--bg-main); color: var(--text-main); outline: none;">
                </div>
                
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">End Date</label>
                    <input type="date" name="end_date" id="cleanEnd" required style="width: 100%; padding: 0.85rem; border-radius: 12px; border: 1px solid var(--border-light); background: var(--bg-main); color: var(--text-main); outline: none;">
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="button" onclick="closeCleanupModal()" style="flex: 1; padding: 0.85rem; border-radius: 12px; border: 1px solid var(--border-light); background: white; color: var(--text-muted); font-weight: 700; cursor: pointer;">Cancel</button>
                    <button type="button" onclick="showConfirmStep()" style="flex: 2; padding: 0.85rem; border-radius: 12px; border: none; background: #ef4444; color: white; font-weight: 700; cursor: pointer;">Proceed Delete</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Final Confirmation Modal (Modern Style) -->
    <div id="confirmDeleteModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 10001; align-items: center; justify-content: center;">
        <div class="animate-scale-up" style="background: white; width: 450px; border-radius: 24px; padding: 2.5rem; text-align: center; box-shadow: 0 30px 60px -12px rgba(0,0,0,0.3);">
            <div style="width: 80px; height: 80px; background: #fee2e2; color: #ef4444; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; animation: pulse-red 2s infinite;">
                <i data-lucide="alert-triangle" style="width: 40px; height: 40px;"></i>
            </div>
            
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 0.75rem;">Confirm Permanent Deletion</h3>
            
            <p id="confirmMessage" style="color: #64748b; font-size: 1rem; line-height: 1.6; margin-bottom: 2rem;">
                Are you absolutely sure you want to permanently delete all logs from <strong id="dispStart" style="color: #1e293b;"></strong> to <strong id="dispEnd" style="color: #1e293b;"></strong>?
            </p>
            
            <div style="background: #f8fafc; border-radius: 16px; padding: 1rem; border: 1px dashed #cbd5e1; margin-bottom: 2rem;">
                <p style="color: #ef4444; font-weight: 700; font-size: 0.85rem; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">
                    <i data-lucide="info" style="width: 14px; vertical-align: middle;"></i> This action is irreversible
                </p>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button onclick="closeConfirmStep()" style="flex: 1; padding: 1rem; border-radius: 14px; border: 1px solid var(--border-light); background: white; color: #64748b; font-weight: 700; cursor: pointer; transition: 0.2s;">
                    No, Go Back
                </button>
                <button onclick="submitCleanupForm()" style="flex: 1.5; padding: 1rem; border-radius: 14px; border: none; background: #e11d48; color: white; font-weight: 800; cursor: pointer; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3); transition: 0.2s;">
                    Yes, Delete Everything
                </button>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div id="logsTableContainer" class="animate-slide-up" style="animation-delay: 0.1s;" data-filter-url="{{ route('admin.audit-trail.filter') }}">
        @include('partials.audit-trail-table', ['logs' => $logs])
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/audit-trail.js') }}"></script>
@endpush

<style>
    @keyframes pulse-red {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    .animate-scale-up { animation: scaleUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) both; }
    @keyframes scaleUp { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    @keyframes slideIn { from { transform: translateX(20px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out; }
    .animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
