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
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                    <div id="realtime-status-archive"
                        style="display: flex; align-items: center; gap: 0.6rem; padding: 0.5rem 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 100px; color: #16a34a; font-size: 0.75rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase;">
                        <span
                            style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block; animation: pulse-green-archive 2s infinite;"></span>
                        Real-time Active
                    </div>
                </div>
            </div>
        </div>

        <style>
            @keyframes pulse-green-archive {
                0% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
                }

                70% {
                    transform: scale(1.2);
                    box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
                }

                100% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
                }
            }
        </style>

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
        <div class="archive-action-bar" style="margin-top: 0.5rem; margin-bottom: 1rem;">
            <!-- Search bar -->
            <form id="searchForm" method="GET" action="{{ route('employees.archive') }}" style="margin: 0; padding: 0;">
                <input type="hidden" name="tab" id="searchTabInput" value="{{ $active_tab }}">
                <div class="archive-search">
                    <i data-lucide="search" class="search-icon"></i>
                    <input type="text" name="search" id="archiveSearchInput" class="search-input-modern"
                        placeholder="Search archive..." value="{{ $search }}" oninput="liveSearch(this.value)"
                        autocomplete="off">
                </div>
            </form>

            <div class="archive-actions-right" style="display: flex; gap: 0.75rem;">
                <!-- Filter Dropdown -->
                <div class="archive-filters-dropdown">
                    <button title="Filter by Date" class="btn btn-outline filter-toggle-btn" onclick="toggleFilterMenu()"
                        style="border-radius: 12px; height: 3rem; padding: 0 1rem;">
                        <i data-lucide="calendar-check"></i>
                        <i data-lucide="chevron-down" class="chevron"></i>
                    </button>
                    <div id="filterMenu" class="filter-menu-dropdown">
                        <form id="filterForm" method="GET" action="{{ route('employees.archive') }}" class="filters-form">
                            @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                            <input type="hidden" name="tab" id="filterTabInput" value="{{ $active_tab }}">

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
                                        <option value="{{ $index + 1 }}" {{ request('month') == ($index + 1) ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-group-item">
                                <label>Specific Date</label>
                                <input type="date" name="date" class="filter-date" value="{{ request('date') }}"
                                    onchange="submitWithFilter()">
                            </div>

                            <div class="filter-menu-footer">
                                <a href="{{ route('employees.archive') }}?tab={{ $active_tab }}" class="btn-reset-filters"
                                    onclick="localStorage.setItem('archiveFilterOpen', 'false')">
                                    <i data-lucide="filter-x"></i>
                                    Reset Filters
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Export Button -->
                @php
                    $currentUser = \App\Models\User::find(session('auth_user_id'));
                    $canExportArchive = $currentUser && $currentUser->hasPermission('export_archive');
                @endphp
                @if($canExportArchive)
                    <div class="archive-filters-dropdown">
                        <button title="Export" class="btn btn-outline" onclick="openExportModal()"
                            style="border-radius: 12px; height: 3rem; padding: 0 1.25rem; display: flex; align-items: center; gap: 0.5rem; background: #3b82f6; border: 1px solid #3b82f6; font-weight: 700; font-size: 0.85rem; color: white; transition: 0.2s;">
                            <i data-lucide="external-link" style="width: 18px; color: white;"></i>
                        </button>
                    </div>
                @endif

                <!-- Report Generator Button -->
                @if($canExportArchive)
                    <div class="archive-filters-dropdown">
                        <button title="Generate Report" class="btn btn-outline" onclick="openReportModal()"
                            style="border-radius: 12px; height: 3rem; padding: 0 1.25rem; display: flex; align-items: center; gap: 0.5rem; background: var(--bg-card); border: 1px solid var(--border); font-weight: 700; font-size: 0.85rem; color: var(--text-main); transition: 0.2s;">
                            <i data-lucide="clipboard-list" style="width: 18px; color: var(--primary);"></i>
                            <span>Report</span>
                        </button>
                    </div>
                @endif

                <!-- Sort Dropdown -->
                <div class="archive-filters-dropdown">
                    <button class="btn btn-outline sort-toggle-btn" onclick="toggleSortMenu()"
                        style="border-radius: 12px; height: 3rem; padding: 0 1.25rem; display: flex; align-items: center; gap: 0.5rem; background: var(--bg-card); border: 1px solid var(--border); font-weight: 700; font-size: 0.85rem; color: var(--text-main);">
                        <i data-lucide="list-filter" style="width: 18px;"></i>
                        <span>Sort</span>
                        <i data-lucide="chevron-down" class="chevron" style="width: 16px; opacity: 0.5;"></i>
                    </button>
                    <div id="sortMenu" class="filter-menu-dropdown sort-dropdown modern-sort-menu"
                        style="width: 220px; padding: 0.5rem;">
                        <div class="sort-menu-header"
                            style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border); margin-bottom: 0.5rem;">
                            <span
                                style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Sort
                                By</span>
                        </div>
                        <div class="sort-options" style="display: flex; flex-direction: column; gap: 2px;">
                            <!-- Separation Date Sort -->
                            <button
                                class="sort-opt-btn {{ (request('sort', 'recent') == 'recent' || request('sort') == 'sep_desc') ? 'active' : '' }}"
                                onclick="submitWithSort('sep_desc')">
                                <i data-lucide="calendar-days"></i> <span>Separation Date (Newest)</span>
                            </button>
                            <button class="sort-opt-btn {{ request('sort') == 'sep_asc' ? 'active' : '' }}"
                                onclick="submitWithSort('sep_asc')">
                                <i data-lucide="history"></i> <span>Separation Date (Oldest)</span>
                            </button>

                            <div style="height: 1px; background: var(--border); margin: 4px 0.5rem; opacity: 0.5;"></div>

                            <!-- Archived Date Sort -->
                            <button class="sort-opt-btn {{ request('sort') == 'archived_recent' ? 'active' : '' }}"
                                onclick="submitWithSort('archived_recent')">
                                <i data-lucide="archive"></i> <span>Date Archived (Newest)</span>
                            </button>
                            <button class="sort-opt-btn {{ request('sort') == 'archived_oldest' ? 'active' : '' }}"
                                onclick="submitWithSort('archived_oldest')">
                                <i data-lucide="clock"></i> <span>Date Archived (Oldest)</span>
                            </button>

                            <div style="height: 1px; background: var(--border); margin: 4px 0.5rem; opacity: 0.5;"></div>

                            <button class="sort-opt-btn {{ request('sort') == 'name' ? 'active' : '' }}"
                                onclick="submitWithSort('name')">
                                <i data-lucide="type"></i> <span>Alphabetical (A-Z)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabbed Navigation -->
        <div class="archive-tabs-container" style="margin-top: 0.5rem; margin-bottom: 2rem;">
            <div class="archive-tabs sliding-tabs">
                <!-- Active Slide Background -->
                <div id="tab-slider" class="tab-slider"></div>

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
                <button class="archive-tab" onclick="switchTab('reports')" id="btn-reports">
                    <i data-lucide="file-text"></i>
                    <span>Reports</span>
                </button>
            </div>
        </div>

        <!-- Tab Panels -->
        <div class="tab-panels-wrapper" style="margin-top: 1rem;">
            <div id="panelsContainer" class="tab-panels" data-active-tab="{{ $active_tab }}"
                data-export-url="{{ route('employees.archive.export.json') }}"
                data-report-url="{{ route('employees.archive.export.json') }}?tab=all"
                data-report-store-url="{{ route('archive.reports.store') }}"
                data-reports-list-url="{{ route('archive.reports.index') }}"
                data-reported-ids-url="{{ route('archive.reported-ids') }}" data-report-delete-url="/archive/reports"
                data-csrf-token="{{ csrf_token() }}">
                @include('partials.archive-panels')
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

            .pointer {
                cursor: pointer;
            }

            /* Archive Action Bar - REFINED */
            .archive-action-bar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1.5rem;
                padding: 0.5rem 0;
                margin-bottom: 2rem;
                position: relative;
                z-index: 50;
                /* Ensure dropdown stays above table */
            }

            .archive-search {
                position: relative;
                width: 380px;
                /* Reduced width */
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
                z-index: 1000;
                /* High z-index */
                display: none;
                animation: scaleIn 0.2s ease-out;
            }

            @keyframes scaleIn {
                from {
                    opacity: 0;
                    transform: scale(0.95) translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
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

            .filter-select,
            .filter-date {
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

            .btn-reset-filters:hover {
                opacity: 0.8;
            }

            /* Archive Tabs - SLIDING STYLE (Request Center) */
            .archive-tabs-container {
                margin-bottom: 2rem;
                margin-top: 1rem;
            }

            .archive-tabs.sliding-tabs {
                display: inline-flex;
                background: #f1f5f9;
                padding: 0.375rem;
                border-radius: 18px;
                position: relative;
                border: 2px solid #cbd5e1;
                min-width: 500px;
                box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
            }

            body[data-theme="dark"] .archive-tabs.sliding-tabs {
                background: #1e293b;
                border-color: #334155;
            }

            .tab-slider {
                position: absolute;
                top: 0.375rem;
                bottom: 0.375rem;
                left: 0.375rem;
                width: calc(20% - 0.375rem);
                background: linear-gradient(135deg, #3b82f6, #6366f1);
                border-radius: 14px;
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                z-index: 1;
            }

            .archive-tab {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.625rem;
                padding: 0.75rem 1.25rem;
                border-radius: 12px;
                border: none;
                background: transparent;
                color: #64748b;
                font-weight: 800;
                font-size: 0.875rem;
                cursor: pointer;
                position: relative;
                z-index: 2;
                transition: color 0.3s ease;
                white-space: nowrap;
            }

            body[data-theme="dark"] .archive-tab {
                color: #94a3b8;
            }

            .archive-tab.active {
                color: white !important;
            }

            .archive-tab i {
                width: 1.125rem;
                height: 1.125rem;
            }

            /* Tab Panes Fade Animation - REMOVED for instant search snappy feel */
            .tab-pane {
                display: none;
            }

            .tab-pane.active {
                display: block;
                animation: none !important;
                transition: none !important;
            }

            .hover-row,
            .modern-table,
            .modern-table *,
            .tab-panels,
            .tab-panels * {
                transition: none !important;
                animation: none !important;
                transform: none !important;
            }

            .modern-sort-menu {
                background: var(--bg-card);
                border: 1px solid var(--border);
                border-radius: 16px;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            }

            .sort-opt-btn {
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
                color: var(--text-muted);
                text-align: left;
            }

            .sort-opt-btn:hover {
                background: var(--bg-main);
                color: var(--text-main);
            }

            .sort-opt-btn.active {
                background: var(--bg-main);
                color: var(--primary);
            }

            .sort-opt-btn i {
                width: 16px;
                height: 16px;
                opacity: 0.7;
            }

            .sort-opt-btn.active i {
                color: var(--primary);
                opacity: 1;
            }

            .archive-pagination-container {
                margin-top: 1rem;
                display: flex;
                justify-content: flex-end;
            }

            @media (max-width: 1024px) {
                .archive-action-bar {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 1rem;
                }

                .archive-search {
                    width: 100%;
                }

                .filter-menu-dropdown {
                    width: calc(100vw - 40px);
                    right: -10px;
                }

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
    <!-- Export Archive Modal -->
    <div id="archiveExportModal" class="modal">
        <div class="modal-content" style="max-width: 480px; padding: 0;">
            <div class="modal-header"
                style="padding: 1.5rem; border-bottom: 1px solid var(--border); background: var(--bg-card);">
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <h2 class="modal-title" style="font-size: 1.25rem; font-weight: 800; color: var(--text-main);">Export
                        Archive</h2>
                    <p style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Choose filters and document
                        type</p>
                </div>
                <button type="button" class="icon-btn" onclick="closeExportModal()">
                    <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                </button>
            </div>
            <div class="modal-body" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                <div class="grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label"
                            style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Year</label>
                        <select id="exportYear" class="form-input"
                            style="height: 3rem; border-radius: 10px; font-weight: 600;">
                            <option value="all">All Years</option>
                            @for($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"
                            style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Month</label>
                        <select id="exportMonth" class="form-input"
                            style="height: 3rem; border-radius: 10px; font-weight: 600;">
                            <option value="all">All Months</option>
                            @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $name)
                                <option value="{{ $index + 1 }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"
                        style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Tab
                        (Separation Status)</label>
                    <select id="exportTab" class="form-input" style="height: 3rem; border-radius: 10px; font-weight: 600;">
                        <option value="all">All Tabs</option>
                        <option value="resign">Resign</option>
                        <option value="retired">Retired</option>
                        <option value="transfer">Transfer</option>
                        <option value="others">Others</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 0.5rem;">
                    <label class="form-label"
                        style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">Choose
                        Export Format</label>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <button onclick="startExport('excel')" class="export-type-btn"
                            style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.25rem; border: 1px solid var(--border); border-radius: 16px; background: var(--bg-card); cursor: pointer; transition: 0.2s;">
                            <i data-lucide="file-spreadsheet" style="width: 24px; height: 24px; color: #10b981;"></i>
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-main);">Excel</span>
                        </button>
                        <button onclick="startExport('pdf')" class="export-type-btn"
                            style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.25rem; border: 1px solid var(--border); border-radius: 16px; background: var(--bg-card); cursor: pointer; transition: 0.2s;">
                            <i data-lucide="file-text" style="width: 24px; height: 24px; color: #ef4444;"></i>
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-main);">PDF</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer"
                style="padding: 1rem 1.5rem; border-top: 1px solid var(--border); background: #f8fafc; border-bottom-left-radius: var(--radius-lg); border-bottom-right-radius: var(--radius-lg);">
                <button type="button" class="btn btn-outline" onclick="closeExportModal()"
                    style="width: 100%; height: 3rem; border-radius: 12px; font-weight: 700;">Cancel</button>
            </div>
        </div>
    </div>
    <style>
        .export-type-btn:hover {
            border-color: #3b82f6 !important;
            transform: translateY(-4px);
        }
    </style>

    <!-- Report Viewer Modal -->
    <div id="reportViewModal" class="modal">
        <div class="modal-content"
            style="max-width: 1200px; width: 95%; height: 95%; max-height: 95%; padding: 0; display: flex; flex-direction: column;">
            <div class="modal-header"
                style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: var(--bg-card);">
                <div style="display: flex; flex-direction: column;">
                    <h2 id="reportViewTitle"
                        style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0;">Report Preview</h2>
                    <span id="reportViewSub" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Loading
                        document...</span>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <button class="btn btn-outline" onclick="closeReportViewModal()"
                        style="padding: 0.5rem; width: 2.5rem; height: 2.5rem; border-radius: 8px;">
                        <i data-lucide="x" style="width: 20px;"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body"
                style="padding: 0; flex: 1; overflow: hidden; background: #64748b; display: flex; flex-direction: column; position: relative;">
                <div id="reportViewLoading"
                    style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; z-index: 10; gap: 1rem; color: var(--text-muted);">
                    <i data-lucide="loader" class="animate-spin" style="width: 3rem; height: 3rem;"></i>
                    <p style="font-weight: 700;">Preparing Preview...</p>
                </div>
                <!-- PDF Container -->
                <iframe id="reportViewIframe" style="width: 100%; height: 100%; border: none; display: none;"></iframe>
                <!-- Excel Container -->
                <div id="reportViewExcel"
                    style="width: 100%; height: 100%; overflow: auto; background: #f1f5f9; display: none; padding: 20px;">
                    <div
                        style="background: white; border: 1px solid #ccc; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: fit-content; min-width: 100%;">
                        <table id="excelPreviewTable" class="gsheet-style-table"></table>
                    </div>
                </div>
            </div>

            <style>
                .gsheet-style-table {
                    border-collapse: collapse;
                    background: white;
                    font-family: 'Arial Narrow', Arial, sans-serif;
                    font-size: 11px;
                    width: 100%;
                }

                .gsheet-style-table th,
                .gsheet-style-table td {
                    border: 1px solid #e2e8f0;
                    padding: 8px 10px;
                    color: #1e293b;
                    outline: none;
                }

                .gsheet-style-table td[contenteditable="true"]:focus {
                    background: #fff !important;
                    box-shadow: inset 0 0 0 2px #3b82f6;
                    z-index: 10;
                    position: relative;
                }

                .gsheet-style-table tbody tr:hover td {
                    background: #f8fafc;
                }

                .gsheet-style-table thead tr:first-child th {
                    background: #f8fafc;
                    font-weight: 800;
                    font-size: 13px;
                }

                .gsheet-col-header {
                    background: #f1f5f9;
                    color: #64748b;
                    font-weight: 600;
                    text-align: center;
                    font-size: 10px;
                    width: 30px;
                    pointer-events: none;
                }

                .gsheet-row-header {
                    background: #f1f5f9;
                    color: #64748b;
                    font-weight: 600;
                    text-align: center;
                    width: 30px;
                    border-right: 2px solid #cbd5e1 !important;
                    pointer-events: none;
                }
            </style>
            <div class="modal-footer"
                style="padding: 0.75rem 1.5rem; border-top: 1px solid var(--border); background: var(--bg-card); display: flex; justify-content: flex-end; gap: 1rem;">
                <button id="downloadEditedBtn" class="btn btn-primary" onclick="redownloadCurrentViewReport()"
                    style="padding: 0.5rem 1.25rem; border-radius: 10px; font-weight: 700;">
                    <i data-lucide="save" style="width: 18px; margin-right: 4px;"></i> Download Edited File
                </button>
            </div>
        </div>
    </div>

    <!-- Generate Report Selection Modal -->
    <div id="reportSelectionModal" class="modal">
        <div class="modal-content"
            style="max-width: 1100px; padding: 0; display: flex; flex-direction: column; max-height: 95vh;">
            <div class="modal-header"
                style="padding: 1.5rem; border-bottom: 1px solid var(--border); background: var(--bg-card); display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <h2 class="modal-title" style="font-size: 1.25rem; font-weight: 800; color: var(--text-main);">Custom
                        Report Generator</h2>
                    <p style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Select names to include in the
                        report</p>
                </div>
                <button type="button" class="icon-btn" onclick="closeReportModal()">
                    <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                </button>
            </div>

            <div class="modal-body"
                style="padding: 1.5rem; flex: 1; overflow-y: hidden; display: flex; flex-direction: column; gap: 1rem;">
                <!-- Search Filter -->
                <div style="position: relative;">
                    <i data-lucide="search"
                        style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); width: 18px; height: 18px;"></i>
                    <input type="text" id="reportSearchFilter" oninput="filterReportList()"
                        placeholder="Search employee name..."
                        style="width: 100%; height: 3rem; padding: 0 1rem 0 3rem; border: 1px solid var(--border); border-radius: 12px; font-size: 0.9rem; background: var(--bg-main); color: var(--text-main); outline: none;">
                </div>

                <!-- Tab Filters -->
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; padding-bottom: 4px;" id="reportTabFilters">
                    <button class="btn btn-primary"
                        style="border-radius: 8px; font-size: 0.75rem; padding: 0.5rem 1rem; border: none; font-weight: 700;"
                        onclick="setReportTab('all', this)">All Tabs</button>
                    <button class="btn btn-outline"
                        style="border-radius: 8px; font-size: 0.75rem; padding: 0.5rem 1rem; font-weight: 600;"
                        onclick="setReportTab('resign', this)">Resigned</button>
                    <button class="btn btn-outline"
                        style="border-radius: 8px; font-size: 0.75rem; padding: 0.5rem 1rem; font-weight: 600;"
                        onclick="setReportTab('retired', this)">Retired</button>
                    <button class="btn btn-outline"
                        style="border-radius: 8px; font-size: 0.75rem; padding: 0.5rem 1rem; font-weight: 600;"
                        onclick="setReportTab('transfer', this)">Transferred</button>
                    <button class="btn btn-outline"
                        style="border-radius: 8px; font-size: 0.75rem; padding: 0.5rem 1rem; font-weight: 600;"
                        onclick="setReportTab('others', this)">Others</button>
                </div>

                <div
                    style="display: flex; gap: 2rem; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
                    <!-- Format Choice -->
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <label style="font-size: 0.85rem; font-weight: 800; color: var(--text-main);">Format:</label>
                        <select id="reportFormat"
                            style="height: 2.5rem; padding: 0 1rem; border-radius: 8px; border: 1px solid var(--border); font-size: 0.85rem; font-weight: 700; outline: none; background: var(--bg-main); color: var(--primary);">
                            <option value="excel" selected>Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>

                    <!-- Sort Choice -->
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <label style="font-size: 0.85rem; font-weight: 800; color: var(--text-main);">Sort By:</label>
                        <select id="reportModalSort" onchange="filterReportList()"
                            style="height: 2.5rem; padding: 0 1rem; border-radius: 8px; border: 1px solid var(--border); font-size: 0.85rem; font-weight: 700; outline: none; background: var(--bg-main); color: var(--text-main);">
                            <option value="sep_newest" selected>Newest by Separation Date</option>
                            <option value="archived_newest">Newest by Date Archived</option>
                        </select>
                    </div>
                </div>

                <div
                    style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; padding-top: 0.5rem;">
                    <!-- Select All Checkbox -->
                    <label
                        style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem; font-weight: 800; cursor: pointer; color: var(--text-main);">
                        <input type="checkbox" id="selectAllReport" onchange="toggleAllReportCheckboxes()"
                            style="width: 16px; height: 16px; accent-color: var(--primary);"> Select / Deselect Selected
                        List
                    </label>
                    <span id="selectedReportCount"
                        style="font-size: 0.75rem; font-weight: 800; color: var(--primary); background: var(--primary-soft); padding: 0.35rem 0.75rem; border-radius: 8px;">0
                        Selected</span>
                </div>

                <!-- List Container -->
                <div id="reportListContainer"
                    style="flex: 1; overflow-y: auto; overflow-x: hidden; display: flex; flex-direction: column; gap: 0.5rem; padding-right: 0.5rem; min-height: 250px; max-height: 500px;">
                    <div id="reportLoadingIndicator"
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: var(--text-muted); gap: 1rem; padding: 2rem;">
                        <i data-lucide="loader" class="animate-spin" style="width: 24px; height: 24px;"></i>
                        <span style="font-size: 0.85rem; font-weight: 600;">Loading records...</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer"
                style="padding: 1rem 1.5rem; border-top: 1px solid var(--border); background: var(--bg-card); display: flex; gap: 1rem;">
                <button type="button" class="btn btn-outline" onclick="closeReportModal()"
                    style="flex: 1; height: 3rem; border-radius: 12px; font-weight: 700;">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="openReportConfirmModal()"
                    style="flex: 1; height: 3rem; border-radius: 12px; font-weight: 700; background: var(--primary); color: white; display: flex; justify-content: center; align-items: center; gap: 0.5rem; border: none; cursor: pointer;">
                    <i data-lucide="download" style="width: 18px;"></i>
                    Generate
                </button>
            </div>
        </div>
    </div>

    <!-- Report Confirmation Modal with Metadata -->
    <div id="reportConfirmModal" class="modal">
        <div class="modal-content" style="max-width: 680px; padding: 0; display: flex; flex-direction: column;">
            <div class="modal-header"
                style="padding: 1.5rem; border-bottom: 1px solid var(--border); background: var(--bg-card); display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <h2 class="modal-title" style="font-size: 1.15rem; font-weight: 800; color: var(--text-main);">Report
                        Details</h2>
                    <p style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Fill in the report header
                        information</p>
                </div>
                <button type="button" class="icon-btn" onclick="closeReportConfirmModal()">
                    <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                </button>
            </div>
            <div class="modal-body" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                <div>
                    <label
                        style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); display: block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Title
                        of Report</label>
                    <input type="text" id="reportTitleInput"
                        value="CONSOLIDATED REPORT ON SEPARATION (Schools Division Office, Quezon City) (Non-Teaching Only)"
                        style="width: 100%; height: 2.75rem; padding: 0 1rem; border: 1px solid var(--border); border-radius: 10px; font-size: 0.85rem; font-weight: 600; outline: none; background: var(--bg-main); color: var(--text-main);">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label
                            style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); display: block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Period
                            Coverage</label>
                        <input type="text" id="reportPeriodInput" placeholder="Auto-generated if left blank"
                            style="width: 100%; height: 2.75rem; padding: 0 1rem; border: 1px solid var(--border); border-radius: 10px; font-size: 0.85rem; font-weight: 600; outline: none; background: var(--bg-main); color: var(--text-main);">
                    </div>
                    <div>
                        <label
                            style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); display: block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Regional
                            Office</label>
                        <input type="text" id="reportOfficeInput" value="CSC FO NIA"
                            style="width: 100%; height: 2.75rem; padding: 0 1rem; border: 1px solid var(--border); border-radius: 10px; font-size: 0.85rem; font-weight: 600; outline: none; background: var(--bg-main); color: var(--text-main);">
                    </div>
                </div>
                <div>
                    <label
                        style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); display: block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em;">File
                        Name</label>
                    <input type="text" id="reportFileNameInput" value="Custom_Separation_Report"
                        style="width: 100%; height: 2.75rem; padding: 0 1rem; border: 1px solid var(--border); border-radius: 10px; font-size: 0.85rem; font-weight: 600; outline: none; background: var(--bg-main); color: var(--text-main);">
                </div>
            </div>
            <div class="modal-footer"
                style="padding: 1rem 1.5rem; border-top: 1px solid var(--border); background: var(--bg-card); display: flex; gap: 1rem;">
                <button type="button" class="btn btn-outline" onclick="closeReportConfirmModal()"
                    style="flex: 1; height: 3rem; border-radius: 12px; font-weight: 700;">Back</button>
                <button type="button" class="btn btn-primary" id="confirmGenerateBtn" onclick="generateReportFinal()"
                    style="flex: 1; height: 3rem; border-radius: 12px; font-weight: 700; background: var(--primary); color: white; display: flex; justify-content: center; align-items: center; gap: 0.5rem; border: none; cursor: pointer;">
                    <i data-lucide="download" style="width: 18px;"></i>
                    Generate Report
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="{{ asset('assets/js/archive.js') }}?v=1.8"></script>
@endpush