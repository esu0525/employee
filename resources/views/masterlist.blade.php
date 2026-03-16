@extends('layouts.app')

@section('title', 'Master List (Boxed)')

@section('content')
<div class="page-content">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Masterlist</h1>
        <p class="page-subtitle">List of active employees.</p>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="search-container">
            <i data-lucide="search" class="search-icon"></i>
            <input 
                type="text" 
                id="searchInput"
                class="search-input" 
                placeholder="Search by name, position, or office..."
                value="{{ $search }}"
                autocomplete="off"
            >
        </div>
        <div class="button-group">
            <button class="btn btn-outline" onclick="openImportModal()">
                <i data-lucide="upload"></i>
                Import
            </button>
            <button id="sortBtn" class="btn {{ $sort === 'position' ? 'btn-primary' : 'btn-outline' }}" onclick="toggleSort()">
                <i data-lucide="{{ $sort === 'position' ? 'briefcase' : 'sort-asc' }}"></i>
                Sort by {{ $sort === 'position' ? 'Position' : 'Name' }}
            </button>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="modal">
        <div class="modal-content" style="max-width: 450px;">
            <form id="importForm" method="POST" action="{{ route('employees.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h2 class="modal-title">Import Masterlist</h2>
                    <button type="button" class="icon-btn" onclick="closeImportModal()">
                        <i data-lucide="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info" style="background: #e0f2fe; color: #0369a1; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.8125rem;">
                        <p style="font-weight: 600; margin-bottom: 0.5rem;">CSV Format Instructions:</p>
                        <ul style="margin-left: 1.25rem;">
                            <li>File must be in <strong>.csv</strong> format</li>
                            <li>Columns: <strong>last_name, first_name, middle_name, position, office</strong></li>
                        </ul>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-input" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeImportModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Start Import</button>
                </div>
            </form>
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
                            <option value="active">Active</option>
                            <option value="transfer">Transfer</option>
                            <option value="retired">Retirement</option>
                            <option value="resign">Resignation</option>
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

                    <div id="retirementFields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">Retirement Under (e.g. 8291/8292)</label>
                            <input type="text" name="retirement_under" class="form-input" placeholder="RA 8291 / RA 1616">
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
    @if(session('success'))
    <div class="toast-notification toast-success" id="successToast">
        <div class="toast-icon"><i data-lucide="check"></i></div>
        <div class="toast-content">
            <p class="toast-title">Import Successful</p>
            <p class="toast-text">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Table Container -->
    <div id="tableContainer">
        @include('partials.masterlist-table')
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Modal Styles */
    .modal { 
        display: none; 
        position: fixed; 
        inset: 0; 
        z-index: 2000; 
        background: rgba(0, 0, 0, 0.7); /* Deep overlay with blur */
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        align-items: center; 
        justify-content: center; 
        padding: 1.5rem; 
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
    
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    .pagination-links a, .pagination-links span {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #64748b;
        font-weight: 500;
        text-decoration: none;
        transition: 0.2s;
        font-size: 0.875rem;
    }
    .pagination-links a:hover {
        background: #f1f5f9;
        border-color: #3b82f6;
        color: #3b82f6;
    }
    .pagination-links .active {
        background: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }

    /* Status Button */
    .btn-update-status {
        display: flex; align-items: center; gap: 0.4rem;
        padding: 0.45rem 0.8rem; border-radius: 8px;
        background: #f1f5f9; border: 1px solid #e2e8f0;
        color: #64748b; font-size: 0.75rem; font-weight: 700;
        cursor: pointer; transition: 0.2s; margin-right: 0.5rem;
    }
    .btn-update-status:hover {
        background: #eef2f6; color: #3b82f6; border-color: #3b82f6;
    }
    .btn-update-status i { width: 14px; height: 14px; }

    /* Success Animation */
    .success-anim-box { display: flex; justify-content: center; }
    .success-circle {
        width: 80px; height: 80px; border-radius: 50%;
        background: #ecfdf5; border: 4px solid #10b981;
        display: flex; align-items: center; justify-content: center;
        animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
    }
    .success-check { color: #10b981; width: 40px; height: 40px; stroke-width: 3; animation: checkAnim 0.6s 0.2s both; }

    @keyframes popIn {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes checkAnim {
        0% { transform: scale(0.5); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endpush

@push('scripts')
<script>
    let searchTimeout = null;
    let currentSort = '{{ $sort }}';

    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchData();
        }, 300);
    });

    function toggleSort() {
        currentSort = currentSort === 'name' ? 'position' : 'name';
        const sortBtn = document.getElementById('sortBtn');
        
        if (currentSort === 'position') {
            sortBtn.className = 'btn btn-primary';
            sortBtn.innerHTML = '<i data-lucide="briefcase"></i> Sort by Position';
        } else {
            sortBtn.className = 'btn btn-outline';
            sortBtn.innerHTML = '<i data-lucide="sort-asc"></i> Sort by Name';
        }
        
        lucide.createIcons();
        fetchData();
    }

    function fetchData(page = 1) {
        const search = document.getElementById('searchInput').value;
        const url = new URL(window.location.href);
        url.searchParams.set('search', search);
        url.searchParams.set('sort', currentSort);
        url.searchParams.set('page', page);

        // Update URL without refreshing
        window.history.pushState({}, '', url);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('tableContainer').innerHTML = html;
            lucide.createIcons();
            attachPaginationLinks();
        });
    }

    function attachPaginationLinks() {
        const links = document.querySelectorAll('.pagination-links a');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page');
                fetchData(page);
            });
        });
    }

    function openImportModal() {
        document.getElementById('importModal').classList.add('active');
        lucide.createIcons();
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.remove('active');
    }

    function openStatusModal(id, name, currentStatus) {
        const modal = document.getElementById('statusModal');
        const form = document.getElementById('statusForm');
        const nameLabel = document.getElementById('statusEmployeeName');
        
        nameLabel.textContent = name;
        form.action = `/employee/update-status/${id}`;
        document.getElementById('statusSelect').value = currentStatus;
        
        handleStatusFields();
        modal.classList.add('active');
        lucide.createIcons();
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.remove('active');
    }

    function handleStatusFields() {
        const status = document.getElementById('statusSelect').value;
        const transferFields = document.getElementById('transferFields');
        const retirementFields = document.getElementById('retirementFields');
        const commonFields = document.getElementById('commonHistoryFields');

        transferFields.style.display = status === 'transfer' ? 'block' : 'none';
        retirementFields.style.display = status === 'retired' ? 'block' : 'none';
        commonFields.style.display = (['transfer', 'retired', 'resign'].includes(status)) ? 'block' : 'none';
    }

    function closeSuccessModal() {
        const modal = document.getElementById('successModal');
        if (modal) modal.classList.remove('active');
    }

    // Initial attachment
    document.addEventListener('DOMContentLoaded', () => {
        attachPaginationLinks();
        
        // Auto-dismiss success toast if it exists (for legacy)
        const toast = document.getElementById('successToast');
        if (toast) {
            setTimeout(() => {
                toast.style.animation = 'toastSlideOut 0.3s ease-in forwards';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
    });
</script>
@endpush
