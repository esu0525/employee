@extends('layouts.app')

@section('title', 'Master List (Boxed)')

@section('content')
<div class="page-content">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Masterlist</h1>
        <p class="page-subtitle">List of active employees.</p>
    </div>

    <!-- Masterlist Summary Cards -->
    <div class="summary-cards-grid" style="grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="summary-card card-primary animate-up" style="--delay: 0.1s;">
            <div class="card-icon-box" style="width: 3.5rem; height: 3.5rem;">
                <i data-lucide="users" style="width: 1.75rem; height: 1.75rem;"></i>
            </div>
            <div class="card-stats">
                <span class="stats-value" style="font-size: 1.75rem;">{{ $total_active }}</span>
                <span class="stats-label">Active Records</span>
            </div>
        </div>
        
        <div class="summary-card card-warning animate-up" style="--delay: 0.2s;">
            <div class="card-icon-box" style="width: 3.5rem; height: 3.5rem;">
                <i data-lucide="sparkles" style="width: 1.75rem; height: 1.75rem;"></i>
            </div>
            <div class="card-stats">
                <span class="stats-value" style="font-size: 1.75rem;">{{ $newly_joined }}</span>
                <span class="stats-label">Recent Hires</span>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar" style="position: relative; z-index: 5;" 
         data-initial-sort="{{ $sort }}" 
         data-initial-category="{{ $categoryFilter ?? '' }}" 
         data-initial-status="{{ $statusFilter ?? '' }}"
         data-export-url="{{ route("api.employees.export.json") }}">
        <div class="search-container">
            <i data-lucide="search" class="search-icon"></i>
            <input 
                type="text" 
                id="searchInput"
                class="search-input" 
                placeholder="Search by name, position, or agency..."
                value="{{ $search }}"
                autocomplete="off"
                oninput="liveSearch(this.value)"
            >
        </div>
        @php
            $currentUser = \App\Models\User::find(session('auth_user_id'));
            $canEditMasterlist = $currentUser && $currentUser->hasPermission('edit_masterlist');
            $canExportMasterlist = $currentUser && $currentUser->hasPermission('export_masterlist');
        @endphp
        <div class="button-group" style="z-index: 1; position: relative;">
            @if($canExportMasterlist)
            <div class="export-dropdown">
                <button class="btn btn-outline" style="border-color: #3b82f6; color: white; background: #3b82f6; transition: 0.2s;" onclick="toggleExportMenu(event)" title="Export">
                    <i data-lucide="external-link" style="color: white; stroke-width: 2.5px;"></i>
                </button>
                <div id="exportMenu" class="export-menu">
                    <button onclick="exportData('excel')">
                        <i data-lucide="file-spreadsheet" style="color: #10b981;"></i>
                        <span>Excel Spreadsheet (.xlsx)</span>
                    </button>
                    <button onclick="exportData('pdf')">
                        <i data-lucide="file-text" style="color: #ef4444;"></i>
                        <span>PDF Document (.pdf)</span>
                    </button>
                    <button onclick="exportData('docs')">
                        <i data-lucide="file-code" style="color: #3b82f6;"></i>
                        <span>Word Document (.doc)</span>
                    </button>
                </div>
            </div>
            @endif


            <div class="sort-dropdown">
                <button id="sortBtn" class="btn btn-outline" onclick="toggleSortMenu(event)" title="Sort & Filter">
                    <i data-lucide="list-filter"></i>
                    <span id="sortLabel">@if($categoryFilter || $statusFilter)Filtered List @else Sort & Filter @endif</span>
                </button>
                <div id="sortMenu" class="export-menu">
                    <div class="menu-section-label">Sorting</div>
                    <button onclick="setSort('name')" class="{{ $sort === 'name' ? 'active-opt' : '' }}">
                        <i data-lucide="sort-asc"></i>
                        <span>A-Z (Name)</span>
                    </button>
                    <button onclick="setSort('name_desc')" class="{{ $sort === 'name_desc' ? 'active-opt' : '' }}">
                        <i data-lucide="sort-desc"></i>
                        <span>Z-A (Name)</span>
                    </button>
                    
                    <div class="menu-section-label">Category Filter</div>
                    <button onclick="setFilter('category', '')">
                        <i data-lucide="layers"></i>
                        <span>All Categories</span>
                    </button>
                    <button onclick="setFilter('category', 'National')" class="{{ $categoryFilter === 'National' ? 'active-opt' : '' }}">
                        <i data-lucide="flag"></i>
                        <span>National Only</span>
                    </button>
                    <button onclick="setFilter('category', 'City')" class="{{ $categoryFilter === 'City' ? 'active-opt' : '' }}">
                        <i data-lucide="building-2"></i>
                        <span>City Only</span>
                    </button>

                    <div class="menu-section-label">Appointment Filter</div>
                    <button onclick="setFilter('status', '')">
                        <i data-lucide="file-stack"></i>
                        <span>All Status</span>
                    </button>
                    <button onclick="setFilter('status', 'Original')" class="{{ $statusFilter === 'Original' ? 'active-opt' : '' }}">
                        <i data-lucide="file-badge"></i>
                        <span>Original Only</span>
                    </button>
                    <button onclick="setFilter('status', 'Permanent')" class="{{ $statusFilter === 'Permanent' ? 'active-opt' : '' }}">
                        <i data-lucide="shield-check"></i>
                        <span>Permanent Only</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <form id="statusForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <div style="display: flex; flex-direction: column;">
                        <h2 class="modal-title">Update Status</h2>
                        <p id="statusEmployeeName" style="font-size: 0.8rem; color: #64748b; font-weight: 600; margin-top: 2px;"></p>
                    </div>
                    <button type="button" class="icon-btn" onclick="closeStatusModal()">
                        <i data-lucide="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">New Status</label>
                        <select name="status" id="statusSelect" class="form-input" required onchange="handleStatusFields()">
                            <option value="" disabled selected>Select Status</option>
                            <option value="transfer">Transfer</option>
                            <option value="retired">Retirement</option>
                            <option value="resign">Resignation</option>
                            <option value="others">Others</option>
                        </select>
                    </div>

                    <!-- Dynamic Fields -->
                    <div id="transferFields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">S.O Number</label>
                            <input type="text" name="so_no" class="form-input" placeholder="e.g. S.O. 1-13 S. 2025">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Transfer To</label>
                            <input type="text" name="transfer_to" class="form-input" placeholder="e.g. DepEd Central Office">
                        </div>
                    </div>



                    <div id="othersFields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">Please Specify</label>
                            <input type="text" name="status_specify" id="status_specify" class="form-input" placeholder="Specify status details...">
                        </div>
                    </div>

                    <div id="commonHistoryFields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">Effective Date</label>
                            <input type="date" name="effective_date" class="form-input" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeStatusModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    @if(session('success_modal'))
    <div id="successModal" class="modal active">
        <div class="modal-content" style="max-width: 400px; text-align: center; padding: 2.5rem 2rem;">
            <div class="success-anim-box">
                <div class="success-circle">
                    <i data-lucide="check" class="success-check"></i>
                </div>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 1.5rem 0 0.5rem; font-family: 'Outfit', sans-serif;">
                {{ session('success_modal.title') }}
            </h2>
            <p style="color: #64748b; font-size: 0.95rem; font-weight: 500; margin-bottom: 2rem; line-height: 1.5;">
                {{ session('success_modal.message') }}
            </p>
            <button type="button" class="btn btn-primary" style="width: 100%; padding: 0.85rem;" onclick="closeSuccessModal()">
                Wonderful!
            </button>
        </div>
    </div>
    @endif


    <!-- Success Toast (reused or local) -->
    <!-- Modern Tiny Success Toast -->
    @if(session('success'))
    <div class="modern-toast-mini active" id="successToast">
        <div class="toast-mini-icon">
            <i data-lucide="check-circle-2"></i>
        </div>
        <div class="toast-mini-content">
            <span class="toast-mini-title">Done!</span>
            <span class="toast-mini-msg">{{ session('success') }}</span>
        </div>
        <button class="toast-mini-close" onclick="closeToast()"><i data-lucide="x"></i></button>
    </div>
    </div>
    @endif

    <!-- Error Toast (Validation Failures) -->
    @if($errors->any())
    <div class="modern-toast-mini active error-toast" id="errorToast" style="background: #fee2e2; border-color: #fca5a5; color: #991b1b;">
        <div class="toast-mini-icon" style="background: #fecaca; color: #ef4444;">
            <i data-lucide="alert-circle"></i>
        </div>
        <div class="toast-mini-content">
            <span class="toast-mini-title" style="color: #7f1d1d;">Update Failed</span>
            <span class="toast-mini-msg" style="color: #991b1b;">{{ $errors->first() }}</span>
        </div>
        <button class="toast-mini-close" onclick="this.parentElement.classList.remove('active')" style="color: #ef4444;"><i data-lucide="x"></i></button>
    </div>
    <script>setTimeout(() => document.getElementById('errorToast')?.classList.remove('active'), 5000);</script>
    <style>
        .error-toast { border-left: 4px solid #ef4444; }
    </style>
    @endif

    <!-- Table Container -->
    <div id="tableContainer">
        @include('partials.masterlist-table')
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Export Dropdown */
    .export-dropdown { position: relative; display: inline-block; z-index: 10; }
    .export-menu {
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 0.5rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2), 0 8px 10px -6px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        width: 240px;
        z-index: 20;
        display: none;
        overflow: hidden;
        padding: 0.5rem;
    }
    .export-menu.active { display: block; animation: dropdown-anim 0.2s ease-out; }
    @keyframes dropdown-anim {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .export-menu button {
        width: 100%;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: none;
        background: none;
        cursor: pointer;
        border-radius: 10px;
        transition: 0.2s;
        font-family: inherit;
        font-size: 0.825rem;
        font-weight: 600;
        color: #475569;
        text-align: left;
    }
    .export-menu button:hover { background: #f1f5f9; color: #1e293b; }
    .export-menu i { width: 18px; height: 18px; }
    .export-menu button.active-opt {
        background: #eef2ff;
        color: #4f46e5;
        font-weight: 800 !important;
        position: relative;
    }
    .export-menu button.active-opt::after {
        content: "";
        position: absolute;
        right: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        background: #4f46e5;
        border-radius: 50%;
    }
    body[data-theme="dark"] .export-menu button.active-opt {
        background: #312e81;
        color: #818cf8;
    }

    .menu-section-label {
        font-size: 0.65rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.75rem 1rem 0.25rem;
    }
    
    .sort-dropdown { position: relative; display: inline-block; z-index: 10; }
    
    body[data-theme="dark"] .export-menu { background: #1e293b; border-color: #334155; }
    body[data-theme="dark"] .export-menu button { color: #94a3b8; }
    body[data-theme="dark"] .export-menu button:hover { background: #334155; color: #f8fafc; }

    /* Modal Styles */
    .modal { 
        display: none; 
        position: fixed; 
        inset: 0; 
        z-index: 2000; 
        background: rgba(15, 23, 42, 0.4); /* Modern deep overlay */
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        align-items: center; 
        justify-content: center; 
        padding: 1.5rem; 
        transition: all 0.3s;
    }
    .modal.active { display: flex; animation: fadeIn 0.3s ease-out; }
    
    .modal-content { 
        background: #ffffff; 
        border-radius: 20px; 
        width: 100%; 
        max-width: 500px;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5); 
        overflow: hidden; 
        border: 1px solid #e2e8f0;
    }
    
    body[data-theme="dark"] .modal-content { background: #1e293b; border-color: #334155; }
    body[data-theme="night"] .modal-content { background: #fffcf0; border-color: #8c7662; }

    .modal-header { 
        padding: 1.25rem 1.5rem; 
        border-bottom: 2px solid #f1f5f9; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        background: #f8fafc; 
    }
    .modal-header .icon-btn {
        border: none !important;
        background: none !important;
        padding: 0.5rem;
        color: #64748b;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-header .icon-btn:hover {
        color: #1e293b;
    }
    body[data-theme="dark"] .modal-header .icon-btn { color: #94a3b8; }
    body[data-theme="dark"] .modal-header .icon-btn:hover { color: #f8fafc; }
    body[data-theme="dark"] .modal-header { background: #0f172a; border-bottom-color: #334155; }
    body[data-theme="night"] .modal-header { background: #fdf6e3; border-bottom-color: #ead6bb; }

    .modal-title { font-size: 1rem; font-weight: 800; color: #1e293b; margin: 0; }
    body[data-theme="dark"] .modal-title { color: #f8fafc; }
    body[data-theme="night"] .modal-title { color: #5c4137; }

    .modal-body { padding: 1.5rem; background: #ffffff; }
    body[data-theme="dark"] .modal-body { background: #1e293b; }
    body[data-theme="night"] .modal-body { background: #fffcf0; }

    .modal-footer { 
        padding: 1.25rem 1.5rem; 
        border-top: 2px solid #f1f5f9; 
        display: flex; 
        justify-content: flex-end; 
        gap: 0.75rem; 
        background: #f8fafc; 
    }
    body[data-theme="dark"] .modal-footer { background: #0f172a; border-top-color: #334155; }
    body[data-theme="night"] .modal-footer { background: #fdf6e3; border-top-color: #ead6bb; }
    
    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.5rem; text-transform: uppercase; }
    body[data-theme="dark"] .form-label { color: #94a3b8; }
    body[data-theme="night"] .form-label { color: #92400e; }

    .form-input { 
        width: 100%; 
        padding: 0.75rem 1rem; 
        border: 2px solid #e2e8f0; 
        border-radius: 10px; 
        outline: none; 
        transition: 0.2s; 
        background: #ffffff;
        color: #1e293b;
    }
    body[data-theme="dark"] .form-input { background: #0f172a; border-color: #334155; color: #f8fafc; }
    body[data-theme="night"] .form-input { background: #fdf6e3; border-color: #ead6bb; color: #000000; }

    .form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    
    /* Modern Nav Buttons */
    .btn-nav {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.75rem 1.25rem;
        border-radius: 14px;
        font-size: 0.875rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid var(--border);
        background: var(--bg-main);
        color: var(--text-muted);
        cursor: pointer;
    }
    
    .btn-nav i { width: 1.125rem; height: 1.125rem; stroke-width: 2.5px; }

    .btn-nav-active:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px -4px rgba(79, 70, 229, 0.4);
    }

    .btn-nav-active:active { transform: translateY(-1px); }

    .btn-nav.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* Status Button - RESTORED EXACTLY */
    .btn-update-status {
        display: flex; 
        align-items: center; 
        gap: 0.4rem;
        padding: 0.45rem 0.8rem; 
        border-radius: 8px;
        background: #f1f5f9; 
        border: 1px solid #e2e8f0;
        color: #64748b; 
        font-size: 0.75rem; 
        font-weight: 700;
        cursor: pointer; 
        transition: 0.2s; 
        margin-right: 0.5rem;
    }
    .btn-update-status:hover {
        background: #eef2f6; 
        color: #3b82f6; 
        border-color: #3b82f6;
    }
    .btn-update-status i { width: 14px; height: 14px; }

    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-up {
        animation: none !important;
    }

    /* Instantly kill any transitions on cards to remove perceived lag */
    .master-item-card, .master-item-card * {
        transition: none !important;
        animation: none !important;
        transform: none !important;
    }

    /* Forcefully remove animations during live search area */
    #tableContainer, #tableContainer * {
        animation: none !important;
        transition: none !important;
        transform: none !important;
    }
</style>
@endpush

@push('scripts')
<!-- Load libraries at top for better availability -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="{{ asset('assets/js/masterlist.js') }}"></script>
@endpush
