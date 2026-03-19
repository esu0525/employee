@extends('layouts.app')

@section('title', 'Archive Dashboard')

@section('content')
<div class="page-content bg-muted/20">
    
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-container">
            <div>
                <h1 class="page-title">Archive</h1>
                <p class="page-subtitle">Manage and view retired, resigned, and historical personnel records.</p>
            </div>
            <div class="header-actions">
                 <button class="btn btn-outline" onclick="document.getElementById('importArchiveInput').click()">
                    <i data-lucide="upload-cloud"></i>
                    Import Records
                </button>
                <form id="importArchiveForm" action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                    @csrf
                    <input type="file" name="csv_file" id="importArchiveInput" onchange="document.getElementById('importArchiveForm').submit()" accept=".csv">
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="summary-cards-grid">
        <!-- Resigned Card -->
        <div class="summary-card card-danger pointer" onclick="switchTab('resign')">
            <div class="card-icon-box">
                <i data-lucide="user-minus"></i>
            </div>
            <div class="card-stats">
                <span class="stats-value">{{ $resign_count }}</span>
                <span class="stats-label">Total Resigned</span>
            </div>
        </div>

        <!-- Retired Card -->
        <div class="summary-card card-warning pointer" onclick="switchTab('retired')">
            <div class="card-icon-box">
                <i data-lucide="award"></i>
            </div>
            <div class="card-stats">
                <span class="stats-value">{{ $retired_count }}</span>
                <span class="stats-label">Total Retired</span>
            </div>
        </div>

        <!-- Transferred Card -->
        <div class="summary-card card-info pointer" onclick="switchTab('transfer')">
            <div class="card-icon-box">
                <i data-lucide="arrow-right-left"></i>
            </div>
            <div class="card-stats">
                <span class="stats-value">{{ $transfer_count }}</span>
                <span class="stats-label">Total Transferred</span>
            </div>
        </div>

        <!-- Others Card -->
        <div class="summary-card card-primary pointer" onclick="switchTab('others')">
            <div class="card-icon-box">
                <i data-lucide="more-horizontal"></i>
            </div>
            <div class="card-stats">
                <span class="stats-value">{{ $others_count }}</span>
                <span class="stats-label">Total Others</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="archive-action-bar">
        <!-- Search bar -->
        <div class="archive-search">
            <i data-lucide="search" class="search-icon"></i>
            <input 
                type="text" 
                id="archiveSearchInput" 
                class="search-input-modern" 
                placeholder="Search personnel..."
                value="{{ $search }}"
                oninput="liveSearch(this.value)"
            >
        </div>

        <!-- Filter Dropdown -->
        <div class="archive-filters-dropdown">
            <button class="btn btn-outline filter-toggle-btn" onclick="toggleFilterMenu()">
                <i data-lucide="filter"></i>
                <span>Filter</span>
                <i data-lucide="chevron-down" class="chevron"></i>
            </button>
            <div id="filterMenu" class="filter-menu-dropdown">
                <form id="filterForm" method="GET" action="{{ route('employees.archive') }}" class="filters-form">
                    @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                    
                    <div class="filter-group-item">
                        <label>Year</label>
                        <select name="year" class="filter-select" onchange="submitWithFilter()">
                            <option value="">All Years</option>
                            @for($y = date('Y'); $y >= date('Y') - 10; $y--)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="filter-group-item">
                        <label>Month</label>
                        <select name="month" class="filter-select" onchange="submitWithFilter()">
                            <option value="">All Months</option>
                            @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $name)
                                <option value="{{ $index + 1 }}" {{ request('month') == ($index + 1) ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group-item">
                        <label>Specific Date</label>
                        <input 
                            type="date" 
                            name="date" 
                            class="filter-date" 
                            value="{{ request('date') }}"
                            onchange="submitWithFilter()"
                        >
                    </div>

                    <div class="filter-menu-footer">
                        <a href="{{ route('employees.archive') }}" class="btn-reset-filters" onclick="localStorage.setItem('archiveFilterOpen', 'false')">
                            <i data-lucide="filter-x"></i>
                            Reset Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabbed Navigation -->
    <div class="archive-tabs-container">
        <div class="archive-tabs">
            <button class="archive-tab" onclick="switchTab('resign')" id="btn-resign">
                <i data-lucide="user-minus"></i>
                <span>Resigned</span>
            </button>
            <button class="archive-tab" onclick="switchTab('retired')" id="btn-retired">
                <i data-lucide="award"></i>
                <span>Retired</span>
            </button>
            <button class="archive-tab" onclick="switchTab('transfer')" id="btn-transfer">
                <i data-lucide="arrow-right-left"></i>
                <span>Transferred</span>
            </button>
            <button class="archive-tab" onclick="switchTab('others')" id="btn-others">
                <i data-lucide="more-horizontal"></i>
                <span>Others</span>
            </button>
        </div>
    </div>

    <!-- Tab Panels -->
    <div class="tab-panels-wrapper">
        <div id="panelsContainer" class="tab-panels">
            <div id="resignTab" class="tab-pane">
                @include('partials.archive-table', ['employees' => $resign, 'type' => 'resign', 'icon' => 'user-minus', 'badge' => 'badge-danger', 'label' => 'Resigned'])
            </div>
            <div id="retiredTab" class="tab-pane">
                @include('partials.archive-table', ['employees' => $retired, 'type' => 'retired', 'icon' => 'award', 'badge' => 'badge-warning', 'label' => 'Retired'])
            </div>
            <div id="transferTab" class="tab-pane">
                @include('partials.archive-table', ['employees' => $transfer, 'type' => 'transfer', 'icon' => 'arrow-right-left', 'badge' => 'badge-info', 'label' => 'Transferred'])
            </div>
            <div id="othersTab" class="tab-pane">
                @include('partials.archive-table', ['employees' => $others, 'type' => 'others', 'icon' => 'more-horizontal', 'badge' => 'badge-primary', 'label' => 'Others'])
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-header-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 2rem;
        gap: 1.5rem;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
    }

    .pointer { cursor: pointer; }

    /* Archive Action Bar - REFINED */
    .archive-action-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        padding: 0.5rem 0;
        margin-bottom: 2rem;
        position: relative;
        z-index: 50; /* Ensure dropdown stays above table */
    }

    .archive-search {
        position: relative;
        width: 380px; /* Reduced width */
    }

    .archive-search .search-icon {
        position: absolute;
        left: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        width: 1.125rem;
        height: 1.125rem;
    }

    .search-input-modern {
        width: 100%;
        height: 3rem;
        padding: 0 1.25rem 0 3.25rem;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        font-size: 0.875rem;
        font-weight: 500;
        outline: none;
        transition: all 0.2s;
    }

    .search-input-modern:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-soft);
    }

    /* Filter Dropdown Styles */
    .archive-filters-dropdown {
        position: relative;
    }

    .filter-toggle-btn {
        height: 3rem;
        padding: 0 1.5rem;
        border-radius: 14px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 700;
        background: var(--bg-card);
    }

    .filter-toggle-btn .chevron {
        width: 14px;
        height: 14px;
        transition: transform 0.3s;
    }

    .filter-toggle-btn.active .chevron {
        transform: rotate(180deg);
    }

    .filter-menu-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 320px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: var(--shadow-xl);
        z-index: 1000; /* High z-index */
        display: none;
        animation: scaleIn 0.2s ease-out;
    }

    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.95) translateY(-10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .filter-menu-dropdown.active {
        display: block;
    }

    .filter-group-item {
        margin-bottom: 1.25rem;
    }

    .filter-group-item label {
        display: block;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .filter-select, .filter-date {
        width: 100%;
        height: 2.75rem;
        padding: 0 1rem;
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-main);
        outline: none;
    }

    .filter-menu-footer {
        padding-top: 1rem;
        border-top: 1px solid var(--border);
        margin-top: 1rem;
    }

    .btn-reset-filters {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--danger);
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        transition: opacity 0.2s;
    }

    .btn-reset-filters:hover { opacity: 0.8; }

    /* Archive Tabs - REFINED */
    .archive-tabs-container { margin-bottom: 2rem; }
    .archive-tabs {
        display: flex; gap: 0.5rem; padding: 0.5rem;
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 18px; width: fit-content;
    }

    .archive-tab {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.625rem 1.25rem; border-radius: 12px;
        border: none; background: transparent; color: var(--text-muted);
        font-weight: 700; font-size: 0.85rem; cursor: pointer;
        transition: all 0.2s;
    }

    .archive-tab.active {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 8px 15px -3px rgba(79, 70, 229, 0.3);
    }

    /* Tab Panes Fade Animation */
    .tab-pane { 
        display: none; 
        opacity: 0;
    }
    
    .tab-pane.active { 
        display: block;
        animation: fadeIn 0.4s ease-out forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @media (max-width: 1024px) {
        .archive-action-bar { flex-direction: column; align-items: stretch; gap: 1rem; }
        .archive-search { width: 100%; }
        .filter-menu-dropdown { width: calc(100vw - 40px); right: -10px; }
        
        .archive-tabs-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 5px;
        }

        .archive-tabs {
            width: max-content;
            display: flex;
            gap: 0.5rem;
            padding: 4px;
        }

        .archive-tab {
            flex-shrink: 0;
            white-space: nowrap;
        }
    }

    @media (max-width: 768px) {
        .page-header-container {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .header-actions {
            width: 100%;
        }

        .header-actions .btn {
            flex: 1;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if(typeof lucide !== 'undefined') lucide.createIcons();
        
        // Always default to 'resign' when visiting Archive for the first time in a session/reload
        const activeTabId = 'resign';
        switchTab(activeTabId, true); // initial call without animation

        // Restore filter menu state
        const isFilterOpen = localStorage.getItem('archiveFilterOpen') === 'true';
        if (isFilterOpen) {
            document.getElementById('filterMenu').classList.add('active');
            document.querySelector('.filter-toggle-btn').classList.add('active');
        }

        // Close filter menu when clicking outside
        window.addEventListener('click', (e) => {
            const menu = document.getElementById('filterMenu');
            const btn = document.querySelector('.filter-toggle-btn');
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.remove('active');
                btn.classList.remove('active');
                localStorage.setItem('archiveFilterOpen', 'false');
            }
        });
    });

    function toggleFilterMenu() {
        const menu = document.getElementById('filterMenu');
        const btn = document.querySelector('.filter-toggle-btn');
        const isActive = menu.classList.toggle('active');
        btn.classList.toggle('active');
        localStorage.setItem('archiveFilterOpen', isActive);
    }

    function submitWithFilter() {
        localStorage.setItem('archiveFilterOpen', 'true');
        document.getElementById('filterForm').submit();
    }

    function switchTab(tabId, immediate = false) {
        const tabs = ['resign', 'retired', 'transfer', 'others'];
        if (!tabs.includes(tabId)) return;

        // Update active states
        document.querySelectorAll('.archive-tab').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
        
        const targetBtn = document.getElementById('btn-' + tabId);
        if(targetBtn) targetBtn.classList.add('active');
        
        const targetPane = document.getElementById(tabId + 'Tab');
        if(targetPane) targetPane.classList.add('active');

        localStorage.setItem('archiveActiveTab', tabId);
    }

    function liveSearch(query) {
        query = query.toLowerCase().trim();
        const tables = document.querySelectorAll('.modern-table');
        
        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr:not(.empty-row)');
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const isMatch = text.includes(query);
                row.style.display = isMatch ? '' : 'none';
                if(isMatch) visibleCount++;
            });

            // Handle empty state manually if everything hidden
            const emptyCell = table.querySelector('.empty-cell');
            if(emptyCell) {
                const parentRow = emptyCell.closest('tr');
                if(visibleCount === 0) {
                    parentRow.style.display = '';
                } else {
                    parentRow.style.display = 'none';
                }
            }
        });
    }
</script>
@endpush
@endsection
