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
    <div class="action-bar" style="position: relative; z-index: 10005;">
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
        <div class="button-group" style="z-index: 10001; position: relative;">
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
                            <li>Columns: <strong>last_name, first_name, middle_name, position, agency</strong></li>
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

                    <div id="retirementFields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">Retirement Under (e.g. 8291/8292)</label>
                            <input type="text" name="retirement_under" class="form-input" placeholder="RA 8291 / RA 1616">
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
    .export-dropdown { position: relative; display: inline-block; z-index: 10002; }
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
        z-index: 10003;
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
    
    body[data-theme="dark"] .export-menu { background: #1e293b; border-color: #334155; }
    body[data-theme="dark"] .export-menu button { color: #94a3b8; }
    body[data-theme="dark"] .export-menu button:hover { background: #334155; color: #f8fafc; }

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
<script>
    let currentSort = '{{ $sort }}';

    function liveSearch(query) {
        query = query.toLowerCase().trim();
        const cards = document.querySelectorAll('.master-item-card');
        const emptyState = document.querySelector('.empty-state');
        let visibleCount = 0;

        // FASTEST possible client-side filtering
        for (let i = 0; i < cards.length; i++) {
            const isMatch = cards[i].textContent.toLowerCase().includes(query);
            cards[i].style.display = isMatch ? 'flex' : 'none';
            if(isMatch) visibleCount++;
        }

        if (emptyState) {
            emptyState.style.display = (visibleCount === 0) ? 'flex' : 'none';
        }

        // Super-charged automatic deep search (150ms)
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchData();
        }, 150);
    }

    // Still allow Enter key to trigger a deep search immediately
    document.getElementById('searchInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimeout);
            fetchData();
        }
    });

    let searchTimeout = null;

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
        const links = document.querySelectorAll('.pagination-ajax');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
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
        const othersFields = document.getElementById('othersFields');
        const commonFields = document.getElementById('commonHistoryFields');

        transferFields.style.display = status === 'transfer' ? 'block' : 'none';
        retirementFields.style.display = status === 'retired' ? 'block' : 'none';
        othersFields.style.display = status === 'others' ? 'block' : 'none';
        commonFields.style.display = (['transfer', 'retired', 'resign', 'others'].includes(status)) ? 'block' : 'none';
    }

    // Close modal on click outside
    window.onclick = function(event) {
        const statusModal = document.getElementById('statusModal');
        const importModal = document.getElementById('importModal');
        const successModal = document.getElementById('successModal');
        const exportMenu = document.getElementById('exportMenu');
        
        if (event.target == statusModal) {
            closeStatusModal();
        }
        if (event.target == importModal) {
            closeImportModal();
        }
        if (event.target == successModal) {
            closeSuccessModal();
        }
        if (exportMenu && !exportMenu.contains(event.target)) {
            exportMenu.classList.remove('active');
        }
    }

    function closeSuccessModal() {
        const modal = document.getElementById('successModal');
        if (modal) modal.classList.remove('active');
    }

        // attach initial links
        attachPaginationLinks();

    // Export Logic
    function toggleExportMenu(e) {
        e.stopPropagation();
        document.getElementById('exportMenu').classList.toggle('active');
    }

    async function exportData(format) {
        document.getElementById('exportMenu').classList.remove('active');
        
        try {
            const response = await fetch('{{ route("employees.export.json") }}');
            if (!response.ok) throw new Error('Status ' + response.status);
            const data = await response.json();
            
            if (format === 'excel') {
                if (typeof ExcelJS === 'undefined') {
                    alert('Excel library is still loading or failed to load. Please try again in a second.');
                    return;
                }
                exportExcel(data);
            } else if (format === 'pdf') {
                exportPDF(data);
            } else if (format === 'docs') {
                exportDocs(data);
            }
        } catch (err) {
            console.error('Export failed:', err);
            alert('Failed to export data: ' + err.message);
        }
    }

    async function exportExcel(data) {
        if (typeof ExcelJS === 'undefined') return;
        
        const workbook = new ExcelJS.Workbook();
        const sheet = workbook.addWorksheet('Active Employees');

        // Add headers
        const headerRow = sheet.addRow(['Full Name', 'Position', 'Agency']);

        // Style headers
        headerRow.eachCell((cell) => {
            cell.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: { argb: 'FF10B981' } // Green background
            };
            cell.font = {
                name: 'Cambria',
                color: { argb: 'FFFFFFFF' }, // White text
                bold: true,
                size: 11
            };
            cell.alignment = { vertical: 'middle', horizontal: 'center' };
            cell.border = {
                top: {style:'thin'},
                left: {style:'thin'},
                bottom: {style:'thin'},
                right: {style:'thin'}
            };
        });

        // Add employee data
        data.forEach(emp => {
            const row = sheet.addRow([emp.name, emp.position, emp.agency]);
            row.eachCell((cell) => {
                cell.font = { name: 'Cambria', size: 11 };
            });
        });

        // Set column widths
        sheet.getColumn(1).width = 45; // Name
        sheet.getColumn(2).width = 40; // Position
        sheet.getColumn(3).width = 50; // Agency

        // Add filters to headers
        sheet.autoFilter = 'A1:C1';

        // Generate and save file
        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/webdriver.ms-excel' });
        saveAs(blob, `Masterlist_Active_Employees_${new Date().toISOString().split('T')[0]}.xlsx`);
    }

    function exportPDF(data) {
        if (typeof jspdf === 'undefined') return;
        const { jsPDF } = jspdf;
        const doc = new jsPDF('l', 'mm', 'a4'); // landscape
        
        // Use Times (Serif) as closest standard font to Cambria in jsPDF
        doc.setFont('times', 'bold');
        doc.setFontSize(22);
        const pageWidth = doc.internal.pageSize.getWidth();
        doc.text('201 Masterlist', pageWidth / 2, 20, { align: 'center' });

        const tableHeaders = [['FULL NAME', 'POSITION', 'AGENCY']];
        const tableBody = data.map(e => [
            e.name, 
            e.position, 
            e.agency
        ]);

        doc.autoTable({
            head: tableHeaders,
            body: tableBody,
            startY: 30,
            theme: 'striped',
            headStyles: { fillColor: [16, 185, 129], textColor: 255, fontStyle: 'bold' }, // #10B981 Green
            styles: { fontSize: 9, cellPadding: 2, font: 'times' }, // Reduced padding & size for smaller height
            columnStyles: {
                0: { cellWidth: 80 }, // Much wider Name column
                1: { cellWidth: 'auto' },
                2: { cellWidth: 'auto' }
            }
        });

        doc.save(`Masterlist_Active_Employees_${new Date().toISOString().split('T')[0]}.pdf`);
    }

    function exportDocs(data) {
        // We'll use a data blob with HTML format which Word opens perfectly as a table
        let html = `
            <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
            <head><meta charset='utf-8'><title>201 Masterlist</title>
            <style>
                table { border-collapse: collapse; width: 100%; font-family: "Cambria", serif; }
                th { background: #10B981; color: white; padding: 6px 12px; border: 1px solid #059669; text-align: left; }
                td { padding: 4px 12px; border: 1px solid #cbd5e1; font-family: "Cambria", serif; font-size: 11pt; }
                h1 { font-family: "Cambria", serif; color: #1e293b; text-align: center; font-size: 24pt; margin-bottom: 20pt; }
            </style>
            </head>
            <body>
            <h1>201 Masterlist</h1>
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Position</th>
                        <th>Agency</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.map(emp => `
                        <tr>
                            <td>${emp.name}</td>
                            <td>${emp.position}</td>
                            <td>${emp.agency}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
            </body>
            </html>
        `;

        const blob = new Blob(['\ufeff', html], { type: 'application/msword' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `Masterlist_Active_Employees_${new Date().toISOString().split('T')[0]}.doc`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Initialize scripts (libraries loaded at top of push)
    (async () => {
        console.log('Export libraries initializing...');
    })();
</script>
@endpush
