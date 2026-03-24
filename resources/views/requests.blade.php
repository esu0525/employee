@extends('layouts.app')

@section('title', 'Request List')

@section('content')
    <div class="page-content" style="padding: 2rem;">
        <!-- Modern Header Section -->
        <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <h1
                    style="font-size: 2.5rem; font-weight: 900; color: #1e1b4b; margin: 0; font-family: 'Outfit', sans-serif; letter-spacing: -0.02em;">
                    Request <span
                        style="background: linear-gradient(135deg, #4f46e5, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Center</span>
                </h1>
                <p style="font-size: 1rem; color: #64748b; margin-top: 0.5rem; font-weight: 500;">Manage and track all
                    document requests in one place</p>
            </div>

        </div>

        <!-- Summary Statistics Dashboard -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 3rem;">
            <!-- Total Requests -->
            <div class="summary-card-modern">
                <div style="display: flex; align-items: center; gap: 1.25rem;">
                    <div class="stat-icon-box" style="background: #eef2ff; color: #6366f1;">
                        <i data-lucide="layers"></i>
                    </div>
                    <div>
                        <span class="stat-label">Total Files</span>
                        <span class="stat-value">{{ $all_requests }}</span>
                    </div>
                </div>
            </div>
            <!-- Pending -->
            <div class="summary-card-modern">
                <div style="display: flex; align-items: center; gap: 1.25rem;">
                    <div class="stat-icon-box" style="background: #fffbeb; color: #f59e0b;">
                        <i data-lucide="clock-3"></i>
                    </div>
                    <div>
                        <span class="stat-label">Pending</span>
                        <span class="stat-value">{{ $pending_count }}</span>
                    </div>
                </div>
            </div>
            <!-- Approved -->
            <div class="summary-card-modern">
                <div style="display: flex; align-items: center; gap: 1.25rem;">
                    <div class="stat-icon-box" style="background: #ecfdf5; color: #10b981;">
                        <i data-lucide="check-circle-2"></i>
                    </div>
                    <div>
                        <span class="stat-label">Approved</span>
                        <span class="stat-value">{{ $approved_count }}</span>
                    </div>
                </div>
            </div>
            <!-- Rejected -->
            <div class="summary-card-modern">
                <div style="display: flex; align-items: center; gap: 1.25rem;">
                    <div class="stat-icon-box" style="background: #fef2f2; color: #ef4444;">
                        <i data-lucide="alert-circle"></i>
                    </div>
                    <div>
                        <span class="stat-label">Rejected</span>
                        <span class="stat-value">{{ $rejected_count }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sliding Tab Switcher & Search Action Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; gap: 1.5rem;">
            <div style="background: #f1f5f9; padding: 0.5rem; border-radius: 20px; display: inline-flex; position: relative; border: 2px solid #cbd5e1; min-width: 400px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);"
                class="tab-switcher-container">
                <!-- Active Slide Background -->
                <div id="tab-slider"
                    style="position: absolute; top: 0.5rem; bottom: 0.5rem; left: 0.5rem; width: calc(50% - 0.5rem); background: linear-gradient(135deg, #3b82f6, #6366f1); border-radius: 16px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 1;">
                </div>

                <button onclick="switchTab('pending')" id="btn-pending" class="tab-btn">
                    Pending
                    <span class="count-badge">{{ $pending_count }}</span>
                </button>
                <button onclick="switchTab('approved')" id="btn-approved" class="tab-btn">
                    Approved
                    <span class="count-badge approved-count">{{ $approved_count }}</span>
                </button>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center; flex: 1; justify-content: flex-end;">
                <div class="search-container"
                    style="width: 350px; position: relative; background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 0.4rem 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: border-color 0.3s; border-width: 2px;">
                    <i data-lucide="search"
                        style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #94a3b8;"></i>
                    <form method="GET" action="{{ route('employees.requests') }}" id="searchForm">
                        <input type="text" id="liveSearchInput" name="search"
                            style="width: 100%; border: none; padding: 0.5rem 0.5rem 0.5rem 2rem; font-size: 0.95rem; font-family: 'Inter', sans-serif; outline: none; background: transparent; color: #1e293b;"
                            placeholder="Search requests..." value="{{ $search }}" oninput="liveSearch(this.value)">
                    </form>
                </div>

                <!-- Filter Dropdown (Archive Style) -->
                <div style="position: relative;" class="archive-filters-dropdown">
                    <button class="filter-toggle-btn" onclick="toggleFilterMenu()"
                        style="height: 3.2rem; padding: 0 1.5rem; border-radius: 16px; display: flex; align-items: center; gap: 0.75rem; font-weight: 700; background: white; border: 2px solid #e2e8f0; cursor: pointer; transition: all 0.3s; color: #475569;">
                        <i data-lucide="filter" style="width: 18px; height: 18px;"></i>
                        <span>Filter</span>
                        <i data-lucide="chevron-down" style="width: 14px; height: 14px; transition: transform 0.3s;"
                            class="chevron"></i>
                    </button>
                    <div id="filterMenu" class="filter-menu-dropdown"
                        style="position: absolute; top: calc(100% + 10px); right: 0; width: 300px; background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); z-index: 1000; display: none; animation: scaleIn 0.2s ease-out;">
                        <form id="filterForm" method="GET" action="{{ route('employees.requests') }}">
                            @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif

                            <div style="margin-bottom: 1.25rem;">
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Year</label>
                                <select name="year"
                                    style="width: 100%; height: 2.75rem; padding: 0 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; outline: none;"
                                    onchange="this.form.submit()">
                                    <option value="">All Years</option>
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div style="margin-bottom: 1.25rem;">
                                <label
                                    style="display: block; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Document
                                    Type</label>
                                <select name="type"
                                    style="width: 100%; height: 2.75rem; padding: 0 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; outline: none;"
                                    onchange="this.form.submit()">
                                    <option value="">All Documents</option>
                                    <option value="Service Record" {{ request('type') == 'Service Record' ? 'selected' : '' }}>Service Record</option>
                                    <option value="Certificate of Employment" {{ request('type') == 'Certificate of Employment' ? 'selected' : '' }}>COE</option>
                                </select>
                            </div>

                            <div style="padding-top: 1rem; border-top: 1px solid #f1f5f9; margin-top: 1rem;">
                                <a href="{{ route('employees.requests') }}"
                                    style="display: flex; align-items: center; gap: 0.5rem; color: #ef4444; font-size: 0.8rem; font-weight: 700; text-decoration: none;">
                                    <i data-lucide="filter-x" style="width: 14px; height: 14px;"></i>
                                    Reset Filters
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Contents -->
        <div id="tab-pending" class="tab-pane active">
            <div class="table-card">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0 12px;">
                    <thead>
                        <tr style="background: transparent;">
                            <th class="th-mod">ID</th>
                            <th class="th-mod">Employee Details</th>
                            <th class="th-mod">Document & Purpose</th>
                            <th class="th-mod" style="text-align: center;">Copies</th>
                            <th class="th-mod" style="text-align: center;">Date Filed</th>
                            <th class="th-mod" style="text-align: center;">Attachment</th>
                            <th class="th-mod" style="text-align: center;">Quick Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $req)
                            <tr class="tr-row clickable-row" onclick="showRequestDetails({
                                    id: '{{ $req->id }}',
                                    name: '{{ $req->employee_name }}',
                                    agency: '{{ $req->agency ?? 'Unspecified' }}',
                                    type: '{{ $req->request_type }}',
                                    purpose: '{{ $req->purpose ?? 'General' }}',
                                    copies: '{{ $req->num_copies ?? 1 }}',
                                    date: '{{ $req->request_date->format('M d, Y') }}',
                                    file: '{{ $req->requirements_file ? asset($req->requirements_file) : '' }}'
                                })">
                                <td style="padding: 1.25rem 1.5rem;">
                                    <span class="id-badge">#{{ $req->id }}</span>
                                </td>
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                        <span class="employee-name-plain">{{ $req->employee_name }}</span>
                                        <span class="agency-label"><i
                                                data-lucide="building"></i>{{ $req->agency ?? 'Unspecified' }}</span>
                                    </div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                        <span class="doc-type">{{ $req->request_type }}</span>
                                        <span class="purpose-text"><i
                                                data-lucide="help-circle"></i>{{ $req->purpose ?? 'General' }}</span>
                                    </div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                    <div class="copy-badge">{{ $req->num_copies ?? 1 }}</div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                    <span class="date-text">{{ $req->request_date->format('M d, Y') }}</span>
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                    @if($req->requirements_file)
                                        <button
                                            onclick="event.stopPropagation(); showAttachment('{{ asset($req->requirements_file) }}')"
                                            class="btn-attachment">
                                            <i data-lucide="paperclip"></i> View
                                        </button>
                                    @else
                                        <span class="no-attach">None</span>
                                    @endif
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                    <div style="display: flex; gap: 0.75rem; justify-content: center;"
                                        onclick="event.stopPropagation()">
                                        <form action="{{ route('requests.approve', $req->id) }}" method="POST">@csrf
                                            <button type="submit" class="btn-action approve" title="Approve Request"><i
                                                    data-lucide="check"></i></button>
                                        </form>
                                        <form action="{{ route('requests.reject', $req->id) }}" method="POST">@csrf
                                            <button type="submit" class="btn-action reject" title="Reject Request"><i
                                                    data-lucide="x"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row" id="pending-empty-state">
                                <td colspan="7">
                                    <div class="empty-state-modern"
                                        style="padding: 6rem 2rem; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; animation: fadeInUp 0.6s ease-out;">
                                        <div class="empty-icon-wrapper"
                                            style="width: 120px; height: 120px; background: linear-gradient(135deg, #f0fdf4, #dcfce7); border-radius: 40px; display: flex; align-items: center; justify-content: center; margin-bottom: 2rem; border: 1px solid #bbf7d0; box-shadow: 0 15px 35px -10px rgba(16, 185, 129, 0.15);">
                                            <i data-lucide="sparkles" style="width: 55px; height: 55px; color: #10b981;"></i>
                                        </div>
                                        <h3
                                            style="font-size: 1.75rem; font-weight: 900; color: #1e293b; margin: 0 0 0.75rem 0; font-family: 'Outfit', sans-serif; letter-spacing: -0.02em;">
                                            Workspace is Clear!</h3>
                                        <p
                                            style="color: #64748b; font-size: 1.125rem; font-weight: 500; max-width: 400px; line-height: 1.6;">
                                            No pending requests at the moment. You're all caught up with your tasks!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-approved" class="tab-pane">
            <div class="table-card">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0 12px;">
                    <thead>
                        <tr style="background: transparent;">
                            <th class="th-mod">ID</th>
                            <th class="th-mod">Employee / Agency</th>
                            <th class="th-mod">Document / Purpose</th>
                            <th class="th-mod" style="text-align: center;">Copies</th>
                            <th class="th-mod" style="text-align: center;">Approved Date</th>
                            <th class="th-mod" style="text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($approved_requests as $req)
                            <tr class="tr-row approved-hover">
                                <td style="padding: 1.25rem 1.5rem;"><span class="id-badge approved">#{{ $req->id }}</span></td>
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                        <span class="employee-name-plain">{{ $req->employee_name }}</span>
                                        <span class="agency-label"><i
                                                data-lucide="building"></i>{{ $req->agency ?? 'Unspecified' }}</span>
                                    </div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                        <span class="doc-type-approved">{{ $req->request_type }}</span>
                                        <span class="purpose-text"><i
                                                data-lucide="help-circle"></i>{{ $req->purpose ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                    <div class="copy-badge">{{ $req->num_copies ?? 1 }}</div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align: center;"><span
                                        class="date-text">{{ $req->updated_at->format('M d, Y') }}</span></td>
                                <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                    <span class="status-badge approved"><i data-lucide="check-circle"></i> Approved</span>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row" id="approved-empty-state">
                                <td colspan="6">
                                    <div class="empty-state-modern"
                                        style="padding: 6rem 2rem; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; animation: fadeInUp 0.6s ease-out;">
                                        <div class="empty-icon-wrapper"
                                            style="width: 120px; height: 120px; background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 40px; display: flex; align-items: center; justify-content: center; margin-bottom: 2rem; border: 1px solid #e2e8f0; box-shadow: 0 15px 35px -10px rgba(0,0,0,0.05);">
                                            <i data-lucide="history" style="width: 55px; height: 55px; color: #94a3b8;"></i>
                                        </div>
                                        <h3
                                            style="font-size: 1.75rem; font-weight: 900; color: #1e293b; margin: 0 0 0.75rem 0; font-family: 'Outfit', sans-serif; letter-spacing: -0.02em;">
                                            No History Yet</h3>
                                        <p
                                            style="color: #64748b; font-size: 1.125rem; font-weight: 500; max-width: 400px; line-height: 1.6;">
                                            Approved requests will appear here once you've processed them.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Request Details Modal (A4 Print Format) -->
    <div id="detailsModal" class="modal-overlay" onclick="closeModal('detailsModal')">
        <div class="modal-card-large" onclick="event.stopPropagation()"
            style="max-height: 95vh; display: flex; flex-direction: column;">
            <div class="modal-header">
                <h2>Official Request View</h2>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <button onclick="printIframe()" class="modal-btn approve"
                        style="padding: 0.6rem 1.5rem; width: auto; font-size: 0.9rem;">
                        <i data-lucide="printer"></i> Print Document
                    </button>
                    <button class="close-btn" onclick="closeModal('detailsModal')">&times;</button>
                </div>
            </div>
            <div class="modal-body-full" style="flex: 1; overflow: hidden; background: #f1f5f9;">
                <iframe id="detailsFrame" src=""
                    style="width: 100%; height: 85vh; border: none; background: white;"></iframe>
            </div>
            <div class="modal-footer" style="padding: 1.25rem 2rem;">
                <div style="display: flex; gap: 1.5rem; align-items: center; justify-content: flex-end; width: 100%;">
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600; flex: 1;">Quick Process:</span>
                    <form id="form-approve" method="POST" style="margin: 0;"> @csrf
                        <button type="submit" class="modal-btn approve" style="padding: 0.75rem 2rem; width: auto;">Approve
                            Now</button>
                    </form>
                    <form id="form-reject" method="POST" style="margin: 0;"> @csrf
                        <button type="submit" class="modal-btn reject" style="padding: 0.75rem 2rem; width: auto;">Reject
                            Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Attachment Preview Modal -->
    <div id="attachmentModal" class="modal-overlay" onclick="closeModal('attachmentModal')">
        <div class="modal-card-large" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h2>Attachment Preview</h2>
                <button class="close-btn" onclick="closeModal('attachmentModal')">&times;</button>
            </div>
            <div class="modal-body-full"
                style="display: flex; align-items: center; justify-content: center; background: white; min-height: 70vh;">
                <iframe id="attachmentFrame" src=""
                    style="width: 100%; height: 75vh; border: none; border-radius: 12px; display: none;"></iframe>
                <div id="attachmentImageContainer"
                    style="width: 100%; height: 75vh; display: flex; align-items: center; justify-content: center; overflow: auto; padding: 1rem;">
                    <img id="attachmentImage" src=""
                        style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.15);">
                </div>
            </div>
        </div>
    </div>

    <style>
        .tab-pane {
            display: none;
            margin-top: 1rem;
        }

        .tab-pane.active {
            display: block;
            animation: tabSlide 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes tabSlide {
            from {
                opacity: 0;
                transform: translateX(10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .summary-card-modern {
            background: white;
            padding: 1.75rem;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .summary-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .stat-icon-box {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-label {
            display: block;
            font-size: 0.875rem;
            color: #334155;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 900;
            color: #0f172a;
            font-family: 'Outfit', sans-serif;
        }

        .tab-btn {
            flex: 1;
            border: none;
            background: transparent;
            padding: 0.85rem 2rem;
            font-size: 1rem;
            font-weight: 800;
            color: #64748b;
            cursor: pointer;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            color: white !important;
        }

        .count-badge {
            background: rgba(0, 0, 0, 0.05);
            color: inherit;
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 0.75rem;
            margin-left: 8px;
            transition: all 0.3s;
        }

        .tab-btn.active .count-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .table-card {
            background: transparent;
        }

        .th-mod {
            padding: 0.75rem 1.5rem;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #475569;
            font-weight: 800;
            border: none;
        }

        .tr-row {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            cursor: pointer;
            border: 2px solid transparent;
        }

        .tr-row:hover {
            background: #fbfbff !important;
            transform: translateY(-4px);
        }

        .tr-row:hover td {
            border-top: 2px solid #8b5cf6 !important;
            border-bottom: 2px solid #8b5cf6 !important;
            color: #1e1b4b;
        }

        .tr-row:hover td:first-child {
            border-left: 2px solid #8b5cf6 !important;
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
        }

        .tr-row:hover td:last-child {
            border-right: 2px solid #8b5cf6 !important;
            border-top-right-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        .tr-row td {
            padding: 1.25rem 1.5rem;
            border-top: 2px solid transparent;
            border-bottom: 2px solid transparent;
        }

        .tr-row td:first-child {
            border-left: 2px solid transparent;
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
            position: relative;
        }

        .tr-row td:last-child {
            border-right: 2px solid transparent;
            border-top-right-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        .tr-row td:first-child::before {
            content: '';
            position: absolute;
            top: 18px;
            bottom: 18px;
            left: 0;
            width: 4px;
            background: linear-gradient(to bottom, #6366f1, #a855f7);
            border-radius: 0 4px 4px 0;
            opacity: 0;
            transition: all 0.3s;
            transform: scaleY(0.4);
        }

        .tr-row:hover td:first-child::before {
            opacity: 1;
            transform: scaleY(1);
        }

        .approved-hover:hover {
            background: #f0fdf4 !important;
            box-shadow: 0 15px 30px -10px rgba(16, 185, 129, 0.15);
        }

        .approved-hover:hover td:first-child::before {
            background: linear-gradient(to bottom, #10b981, #34d399);
        }

        .id-badge {
            font-weight: 800;
            color: #6366f1;
            font-size: 0.875rem;
            padding: 0.4rem 0.6rem;
            background: #f5f3ff;
            border-radius: 8px;
        }

        .id-badge.approved {
            color: #10b981;
            background: #ecfdf5;
        }

        .employee-link {
            text-decoration: none;
            font-size: 1.125rem;
            font-weight: 800;
            color: #1e293b;
            border-bottom: 2px solid transparent;
            width: fit-content;
            transition: 0.2s;
        }

        .employee-link:hover {
            border-bottom-color: #4f46e5;
        }

        .employee-name-plain {
            font-size: 1.05rem;
            font-weight: 800;
            color: #1e293b;
        }

        .agency-label {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .agency-label i {
            width: 14px;
            height: 14px;
        }

        .doc-type {
            font-weight: 700;
            font-size: 1.05rem;
            color: #4f46e5;
        }

        .doc-type-approved {
            font-weight: 700;
            font-size: 1.05rem;
            color: #059669;
        }

        .purpose-text {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
            font-style: italic;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .purpose-text i {
            width: 14px;
            height: 14px;
            position: relative;
            top: 1px;
        }

        .copy-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-weight: 800;
            color: #1e293b;
        }

        .date-text {
            font-weight: 700;
            font-size: 0.9375rem;
            color: #334155;
        }

        .btn-attachment {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            color: #1e293b;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 700;
            transition: 0.2s;
        }

        .btn-attachment:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 16px;
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .btn-action:hover {
            transform: scale(1.05) translateY(-2px);
        }

        .btn-action.approve {
            background: #10b981;
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
        }

        .btn-action.reject {
            background: #ef4444;
            box-shadow: 0 10px 20px -5px rgba(239, 68, 68, 0.4);
        }

        .status-badge.approved {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: #ecfdf5;
            color: #10b981;
            border: 1px solid #d1fae5;
            border-radius: 14px;
            font-size: 0.875rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            animation: modalFade 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            background: white;
            width: 100%;
            max-width: 550px;
            border-radius: 28px;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transform: scale(0.9);
            transition: 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-overlay.active .modal-card {
            transform: scale(1);
        }

        .modal-card-large {
            background: white;
            width: 95%;
            max-width: 1250px;
            border-radius: 28px;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transition: 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-header {
            padding: 1.75rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f1f5f9;
        }

        .modal-header h2 {
            font-size: 1.5rem;
            font-weight: 900;
            color: #1e1b4b;
            margin: 0;
            font-family: 'Outfit', sans-serif;
        }

        .close-btn {
            background: #f1f5f9;
            border: none;
            font-size: 1.5rem;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            transition: 0.2s;
        }

        .close-btn:hover {
            background: #fee2e2;
            color: #ef4444;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-body-full {
            padding: 1rem;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .detail-item label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .detail-item p {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .highlight-text {
            color: #4f46e5 !important;
        }

        .modal-footer {
            padding: 1.5rem 2rem;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
        }

        .modal-btn {
            width: 100%;
            padding: 1rem;
            border-radius: 16px;
            border: none;
            font-weight: 800;
            cursor: pointer;
            transition: 0.3s;
        }

        .modal-btn.approve {
            background: #10b981;
            color: white;
            box-shadow: 0 8px 20px -5px rgba(16, 185, 129, 0.4);
        }

        .modal-btn.reject {
            background: #ef4444;
            color: white;
            box-shadow: 0 8px 20px -5px rgba(239, 68, 68, 0.4);
        }

        .modal-btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        /* Mobile adjustments */
        @media (max-width: 640px) {
            .details-grid {
                grid-template-columns: 1fr;
            }

            .modal-card {
                margin: 1rem;
            }
        }

        /* Dark Mode Overrides */
        /* Dark Mode Overrides - Archive Style Consistency */
        body[data-theme="dark"] .page-content h1 {
            color: #f8fafc !important;
        }

        body[data-theme="dark"] .page-content>div>p {
            color: #94a3b8 !important;
        }

        body[data-theme="dark"] .summary-card-modern {
            background: #1e293b;
            border-color: #334155;
        }

        body[data-theme="dark"] .stat-value {
            color: #f8fafc;
        }

        body[data-theme="dark"] .table-card {
            background: transparent;
            border-color: transparent;
        }

        body[data-theme="dark"] .table-header-row {
            background: transparent !important;
        }

        body[data-theme="dark"] .th-mod {
            color: #94a3b8;
            border-bottom-color: #334155;
        }

        body[data-theme="dark"] .tr-row {
            background: #1e293b;
            border: 1px solid #334155;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
        }

        body[data-theme="dark"] .tr-row td {
            border-color: #334155;
        }

        body[data-theme="dark"] .tr-row:hover {
            background: #2d3748 !important;
            border-color: #8b5cf6;
            box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.4), 0 0 0 2px #8b5cf6;
        }

        body[data-theme="dark"] .tr-row:hover td {
            border-color: #8b5cf6;
        }

        body[data-theme="dark"] .employee-link,
        body[data-theme="dark"] .employee-name-plain,
        body[data-theme="dark"] .date-text,
        body[data-theme="dark"] .copy-badge {
            color: #f8fafc;
        }

        body[data-theme="dark"] .copy-badge {
            background: #334155;
            border-color: #475569;
        }

        body[data-theme="dark"] .agency-label,
        body[data-theme="dark"] .purpose-text {
            color: #94a3b8;
        }

        body[data-theme="dark"] .search-container {
            background: #1e293b !important;
            border-color: #334155 !important;
            border-width: 2px !important;
        }

        body[data-theme="dark"] .filter-toggle-btn {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        body[data-theme="dark"] .filter-menu-dropdown {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        body[data-theme="dark"] .filter-menu-dropdown select {
            background: #0f172a !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        body[data-theme="dark"] .search-container input {
            color: #f8fafc !important;
        }

        body[data-theme="dark"] .tab-switcher-container {
            background: #1e293b !important;
            border-color: #334155 !important;
            border-width: 2px !important;
        }

        body[data-theme="dark"] #tab-slider {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        body[data-theme="dark"] .tab-btn {
            color: #94a3b8 !important;
        }

        body[data-theme="dark"] .tab-btn.active {
            color: white !important;
        }

        body[data-theme="dark"] .modal-card,
        body[data-theme="dark"] .modal-card-large {
            background: #0f172a;
            border: 1px solid #334155;
        }

        body[data-theme="dark"] .modal-header,
        body[data-theme="dark"] .modal-footer {
            border-color: #334155;
            background: #1e293b;
        }

        body[data-theme="dark"] .modal-header h2,
        body[data-theme="dark"] .detail-item p {
            color: #f8fafc;
        }

        body[data-theme="dark"] .close-btn {
            background: #334155;
            color: #94a3b8;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            switchTab('pending'); // Default to pending
        });

        function liveSearch(query) {
            query = query.toLowerCase().trim();
            const tables = document.querySelectorAll('table');

            tables.forEach(table => {
                const rows = table.querySelectorAll('tbody tr:not(.empty-row)');
                const emptyState = table.querySelector('.empty-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const isMatch = text.includes(query);
                    row.style.display = isMatch ? 'table-row' : 'none';
                    if (isMatch) visibleCount++;
                });

                if (emptyState) {
                    if (visibleCount === 0) {
                        emptyState.style.display = 'table-row';
                        // Update text if search was active
                        const title = emptyState.querySelector('h3');
                        const desc = emptyState.querySelector('p');
                        if (query !== '') {
                            if (title) title.innerText = 'No Matches Found';
                            if (desc) desc.innerText = 'We couldn\'t find any requests matching your search term.';
                        } else {
                            // Restore original text based on ID
                            if (emptyState.id === 'pending-empty-state') {
                                if (title) title.innerText = 'Workspace is Clear!';
                                if (desc) desc.innerText = 'No pending requests at the moment. You\'re all caught up!';
                            } else {
                                if (title) title.innerText = 'No History Yet';
                                if (desc) desc.innerText = 'Approved requests will appear here once you\'ve processed them.';
                            }
                        }
                    } else {
                        emptyState.style.display = 'none';
                    }
                }
            });
        }

        function switchTab(tabId) {
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            const target = document.getElementById('tab-' + tabId);
            if (target) target.classList.add('active');

            const slider = document.getElementById('tab-slider');
            const btnPending = document.getElementById('btn-pending');
            const btnApproved = document.getElementById('btn-approved');

            btnPending.classList.remove('active');
            btnApproved.classList.remove('active');

            if (tabId === 'pending') {
                slider.style.left = '0.5rem';
                btnPending.classList.add('active');
            } else {
                slider.style.left = 'calc(50% - 0.5rem + 0.5rem)';
                btnApproved.classList.add('active');

                // Reset approved count badge to 0 when viewed
                const approvedBadge = document.querySelector('.approved-count');
                if (approvedBadge) {
                    approvedBadge.innerText = '0';
                    approvedBadge.style.opacity = '0.5';
                }
            }
            if (window.lucide) lucide.createIcons();
        }

        // Close filter menu when clicking outside
        window.addEventListener('click', (e) => {
            const menu = document.getElementById('filterMenu');
            const btn = document.querySelector('.filter-toggle-btn');
            if (menu && btn && !menu.contains(e.target) && !btn.contains(e.target)) {
                menu.style.display = 'none';
                btn.classList.remove('active');
                const chevron = btn.querySelector('.chevron');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        });

        function showRequestDetails(data) {
            document.getElementById('detailsFrame').src = `/portal/view/${data.id}?compact=1`;

            document.getElementById('form-approve').action = `/requests/${data.id}/approve`;
            document.getElementById('form-reject').action = `/requests/${data.id}/reject`;

            document.getElementById('detailsModal').classList.add('active');
            if (window.lucide) lucide.createIcons();
        }

        function printIframe() {
            const frame = document.getElementById('detailsFrame');
            if (frame.contentWindow) {
                frame.contentWindow.print();
            }
        }

        function showAttachment(url) {
            const frame = document.getElementById('attachmentFrame');
            const imgContainer = document.getElementById('attachmentImageContainer');
            const img = document.getElementById('attachmentImage');

            // Simple extension check
            const isImage = /\.(jpg|jpeg|png|webp|gif|svg)$/i.test(url);

            if (isImage) {
                frame.style.display = 'none';
                imgContainer.style.display = 'flex';
                img.src = url;
            } else {
                imgContainer.style.display = 'none';
                frame.style.display = 'block';
                frame.src = url;
            }

            document.getElementById('attachmentModal').classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
            if (id === 'attachmentModal') {
                document.getElementById('attachmentFrame').src = '';
                document.getElementById('attachmentImage').src = '';
            }
        }
    </script>

    <style>
        @keyframes modalFade {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>

    <style>
        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }

            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 15px rgba(16, 185, 129, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }
    </style>

    <!-- Success Toast -->
    @if(session('success_message'))
        <div id="successToast"
            style="position: fixed; top: 2rem; right: 2rem; z-index: 9999; display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: 12px; box-shadow: 0 20px 60px -15px rgba(0,0,0,0.25); animation: toastSlideIn 0.5s cubic-bezier(0.34,1.56,0.64,1); max-width: 400px; background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #6ee7b7; color: #065f46;">
            <div
                style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background: #10b981; color: white; border-radius: 8px; flex-shrink: 0;">
                <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
            </div>
            <div style="flex: 1;">
                <p style="font-weight: 700; font-size: 0.8125rem;">Success!</p>
                <p style="font-size: 0.75rem; color: #047857;">{{ session('success_message') }}</p>
            </div>
            <button onclick="closeToast()"
                style="background: none; border: none; color: #6ee7b7; cursor: pointer; padding: 0.25rem;">
                <i data-lucide="x" style="width: 14px; height: 14px;"></i>
            </button>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function closeToast() {
            const toast = document.getElementById('successToast');
            if (toast) {
                toast.style.animation = 'toastSlideOut 0.3s ease-in forwards';
                setTimeout(() => toast.remove(), 300);
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
            const toast = document.getElementById('successToast');
            if (toast) setTimeout(() => closeToast(), 4000);
        });
    </script>
@endpush