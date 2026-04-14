@extends('layouts.app')

@section('title', 'Request List')

@section('content')
    @php
        $currentUser = \App\Models\User::find(session('auth_user_id'));
        $canEditRequests = $currentUser && $currentUser->hasPermission('edit_requests');

        if (!function_exists('fixAcronyms')) {
            function fixAcronyms($str)
            {
                if (!$str)
                    return $str;
                return preg_replace_callback('/\b(sdo|dbm|bir|ra|lgu|ict|hr|deped|tor|qc|gsis|pag-ibig)\b/i', function ($matches) {
                    $m = strtolower($matches[1]);
                    if ($m === 'deped')
                        return 'DepEd';
                    return strtoupper($matches[1]);
                }, $str);
            }
        }
    @endphp
    <div class="page-content" style="position: relative; z-index: 1;">
        <!-- Modern Header Section -->
        <div
            style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; gap: 1rem; flex-wrap: wrap;">
            <div>
                <h1
                    style="font-size: 2.5rem; font-weight: 900; color: #1e1b4b; margin: 0 0 0.5rem 0; font-family: 'Outfit', sans-serif; letter-spacing: -0.03em;">
                    Request Center</h1>
                <p
                    style="color: #64748b; font-size: 1.125rem; font-weight: 500; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="file-text" style="width: 18px; height: 18px;"></i> Document Requisition & Tracking
                </p>
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                <div id="realtime-status"
                    style="display: flex; align-items: center; gap: 0.6rem; padding: 0.5rem 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 100px; color: #16a34a; font-size: 0.75rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <span
                        style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block; animation: pulse-green 2s infinite;"></span>
                    Real-time Active
                </div>
            </div>
        </div>

        <style>
            @keyframes pulse-green {
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

        <!-- Summary Statistics Dashboard -->
        <div class="stats-dashboard-grid-compact">
            <!-- Pending -->
            <div class="summary-card-compact card-warning pointer" onclick="switchTab('pending')">
                <div class="card-icon-box-compact">
                    <i data-lucide="clock-3"></i>
                </div>
                <div class="card-stats-compact">
                    <span class="stats-value-compact">{{ $pending_count }}</span>
                    <span class="stats-label-compact">Total Pending</span>
                </div>
            </div>

            <!-- Approved -->
            <div class="summary-card-compact card-success pointer" onclick="switchTab('approved')">
                <div class="card-icon-box-compact">
                    <i data-lucide="check-circle-2"></i>
                </div>
                <div class="card-stats-compact">
                    <span class="stats-value-compact">{{ $approved_count }}</span>
                    <span class="stats-label-compact">Total Approved</span>
                </div>
            </div>
        </div>

        <!-- Sliding Tab Switcher & Search Action Bar -->
        <div class="action-bar-modern">
            <div style="background: #f1f5f9; padding: 0.5rem; border-radius: 20px; display: inline-flex; position: relative; border: 2px solid #cbd5e1; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);"
                class="tab-switcher-container">
                <!-- Active Slide Background -->
                <div id="tab-slider"
                    style="position: absolute; top: 0.5rem; bottom: 0.5rem; left: 0.5rem; width: calc(50% - 0.5rem); background: linear-gradient(135deg, #3b82f6, #6366f1); border-radius: 16px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 1;">
                </div>

                <button onclick="switchTab('pending')" id="btn-pending" class="tab-btn">
                    Pending
                    <span class="count-badge" style="display: none;">{{ $pending_count }}</span>
                </button>
                <button onclick="switchTab('approved')" id="btn-approved" class="tab-btn">
                    Approved
                    <span class="count-badge approved-count" style="display: none;">{{ $approved_count }}</span>
                </button>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center; flex: 1; justify-content: flex-end;">
                <div class="search-container"
                    style="width: 350px; position: relative; background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 0.4rem 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: border-color 0.3s; border-width: 2px;">
                    <i data-lucide="search"
                        style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #94a3b8;"></i>
                    <form method="GET" action="{{ route('employees.requests') }}" id="searchForm">
                        <input type="hidden" name="tab" value="{{ $active_tab }}">
                        <input type="text" id="liveSearchInput" name="search"
                            style="width: 100%; border: none; padding: 0.5rem 0.5rem 0.5rem 2rem; font-size: 0.95rem; font-family: 'Inter', sans-serif; outline: none; background: transparent; color: #1e293b;"
                            placeholder="Search requests..." value="{{ $search }}" oninput="liveSearch(this.value)">
                    </form>
                </div>

                <!-- Filter Dropdown (Archive Style) -->
                <div style="position: relative;" class="archive-filters-dropdown">
                    <button class="filter-toggle-btn" onclick="toggleFilterMenu()" title="Filter by Date"
                        style="height: 3.2rem; padding: 0 1.5rem; border-radius: 16px; display: flex; align-items: center; gap: 0.75rem; font-weight: 700; background: white; border: 2px solid #e2e8f0; cursor: pointer; transition: all 0.3s; color: #475569;">
                        <i data-lucide="calendar" style="width: 18px; height: 18px;"></i>
                        <i data-lucide="chevron-down" style="width: 14px; height: 14px; transition: transform 0.3s;"
                            class="chevron"></i>
                    </button>
                    <div id="filterMenu" class="filter-menu-dropdown"
                        style="position: absolute; top: calc(100% + 10px); right: 0; width: 300px; background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); z-index: 1000; display: none; animation: scaleIn 0.2s ease-out;">
                        <form id="filterForm" method="GET" action="{{ route('employees.requests') }}">
                            <input type="hidden" name="tab" value="{{ $active_tab }}">
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

                            <div style="padding-top: 1rem; border-top: 1px solid #f1f5f9; margin-top: 1rem;">
                                <a href="{{ route('employees.requests', ['tab' => $active_tab]) }}"
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
            <div class="table-card table-responsive">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0 12px; min-width: 900px;">
                    <thead>
                        <tr style="background: transparent;">
                            <th class="th-mod">ID</th>
                            <th class="th-mod">Employee Details</th>
                            <th class="th-mod">Document & Purpose</th>
                            <th class="th-mod" style="text-align: center;">Copies</th>
                            <th class="th-mod" style="text-align: center;">Date Filed</th>
                            <th class="th-mod" style="text-align: center;">Attachment</th>
                            @if($canEditRequests)
                                <th class="th-mod" style="text-align: center;">Quick Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $req)
                            <tr class="tr-row clickable-row" data-id="{{ $req->id }}" data-type="pending"
                                data-timestamp="{{ $req->created_at->timestamp }}" onclick="showRequestDetails({
                                            id: '{{ $req->id }}',
                                            name: '{{ $req->employee_name }}',
                                            agency: '{{ fixAcronyms($req->agency ?? 'Unspecified') }}',
                                            type: '{{ fixAcronyms($req->request_type) }}',
                                            purpose: '{{ fixAcronyms($req->purpose ?? 'General') }}',
                                            copies: '{{ $req->num_copies ?? 1 }}',
                                            date: '{{ $req->request_date->format('M d, Y') }}',
                                            file: '{{ $req->requirements_file ? asset($req->requirements_file) : '' }}'
                                        })">
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div class="row-new-badge">NEW</div>
                                    <span class="id-badge">#{{ $req->id }}</span>
                                </td>
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                        <span class="employee-name-plain">{{ $req->employee_name }}</span>
                                        <span class="agency-label"><i
                                                data-lucide="building"></i>{{ fixAcronyms($req->agency ?? 'Unspecified') }}</span>
                                    </div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                        <span class="doc-type">{{ fixAcronyms($req->request_type) }}</span>
                                        <span class="purpose-text"><i
                                                data-lucide="help-circle"></i>{{ fixAcronyms($req->purpose ?? 'General') }}</span>
                                    </div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                    <div class="copy-badge">{{ $req->num_copies ?? 1 }}</div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                    <div style="display: flex; flex-direction: column; gap: 0.1rem;">
                                        <span class="date-text"
                                            style="font-weight: 800;">{{ $req->created_at->format('M d, Y') }}</span>
                                        <span
                                            style="font-size: 0.7rem; color: #64748b; font-weight: 600;">{{ $req->created_at->format('h:i A') }}</span>
                                    </div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                    @if($req->requirements_file)
                                        <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: center;">
                                            @php $files = explode(';', $req->requirements_file); @endphp
                                            @foreach($files as $index => $file)
                                                <button onclick="event.stopPropagation(); showAttachment('{{ asset($file) }}')"
                                                    class="btn-attachment">
                                                    <i data-lucide="paperclip"></i> File {{ count($files) > 1 ? ($index + 1) : '' }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="no-attach">None</span>
                                    @endif
                                </td>
                                @if($canEditRequests)
                                    <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                        <div style="display: flex; gap: 0.75rem; justify-content: center;"
                                            onclick="event.stopPropagation()">
                                            <form id="quick-approve-{{ $req->id }}"
                                                action="{{ route('requests.approve', $req->id) }}" method="POST">@csrf
                                                <button type="button" class="btn-action approve" title="Approve Request"
                                                    onclick="showConfirmModal('approve', 'quick-approve-{{ $req->id }}')"><i
                                                        data-lucide="check"></i></button>
                                            </form>
                                            <form id="quick-reject-{{ $req->id }}" action="{{ route('requests.reject', $req->id) }}"
                                                method="POST">@csrf
                                                <button type="button" class="btn-action reject" title="Reject Request"
                                                    onclick="showConfirmModal('reject', 'quick-reject-{{ $req->id }}')"><i
                                                        data-lucide="x"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr class="empty-row" id="pending-empty-state">
                                <td colspan="{{ $canEditRequests ? 7 : 6 }}">
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
            @include('partials.requests-pagination', ['employees' => $requests, 'tab' => 'pending'])
        </div>

        <div id="tab-approved" class="tab-pane">
            <div class="table-card table-responsive">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0 12px; min-width: 900px;">
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
                            <tr class="tr-row approved-hover"
                                onclick="showRequestDetails({ id: {{ $req->id }}, status: 'approved' })"
                                data-id="{{ $req->id }}" data-type="approved"
                                data-timestamp="{{ $req->updated_at->timestamp }}">
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div class="row-new-badge">NEW</div>
                                    <span class="id-badge approved">#{{ $req->id }}</span>
                                </td>
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                        <span class="employee-name-plain">{{ $req->employee_name }}</span>
                                        <span class="agency-label"><i
                                                data-lucide="building"></i>{{ fixAcronyms($req->agency ?? 'Unspecified') }}</span>
                                    </div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                        <span class="doc-type-approved">{{ fixAcronyms($req->request_type) }}</span>
                                        <span class="purpose-text"><i
                                                data-lucide="help-circle"></i>{{ fixAcronyms($req->purpose ?? 'N/A') }}</span>
                                    </div>
                                </td>
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                    <div class="copy-badge">{{ $req->num_copies ?? 1 }}</div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align: center;">
                                    <div style="display: flex; flex-direction: column; gap: 0.1rem;">
                                        <span class="date-text"
                                            style="font-weight: 800; color: #059669;">{{ $req->updated_at->format('M d, Y') }}</span>
                                        <span
                                            style="font-size: 0.7rem; color: #64748b; font-weight: 600;">{{ $req->updated_at->format('h:i A') }}</span>
                                    </div>
                                </td>
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
            @include('partials.requests-pagination', ['employees' => $approved_requests, 'tab' => 'approved'])
        </div>
    </div>

    <!-- Request Details Modal (A4 Print Format) -->
    <div id="detailsModal" class="modal-overlay" onclick="closeModal('detailsModal')">
        <div class="modal-card-large" onclick="event.stopPropagation()"
            style="height: 95%; max-height: 95%; width: 95%; max-width: 1250px; display: flex; flex-direction: column;">
            <div class="modal-header">
                <h2>Official Request View</h2>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    @if($canEditRequests)
                        <button id="print-btn-modal" onclick="printIframe()" class="modal-btn approve"
                            style="padding: 0.6rem 1.5rem; width: auto; font-size: 0.9rem;">
                            <i data-lucide="printer"></i> Print Document
                        </button>
                    @endif
                    <button class="close-btn" onclick="closeModal('detailsModal')">&times;</button>
                </div>
            </div>
            <div class="modal-body-full" style="flex: 1; overflow: hidden; background: #f1f5f9; padding: 0;">
                <iframe id="detailsFrame" src=""
                    style="width: 100%; height: 100%; border: none; background: white;"></iframe>
            </div>
            @if($canEditRequests)
                <div id="modal-approval-footer" class="modal-footer" style="padding: 1.25rem 2rem;">
                    <div style="display: flex; gap: 1.5rem; align-items: center; justify-content: flex-end; width: 100%;">
                        <span style="font-size: 0.85rem; color: #64748b; font-weight: 600; flex: 1;">Quick Process:</span>
                        <form id="form-approve" method="POST" style="margin: 0;"> @csrf
                            <button type="button" onclick="approveWithPreparedBy()" class="modal-btn approve"
                                style="padding: 0.75rem 2rem; width: auto;">Approve
                                Now</button>
                        </form>
                        <form id="form-reject" method="POST" style="margin: 0;"> @csrf
                            <button type="button" onclick="rejectRequestWithConfirm()" class="modal-btn reject"
                                style="padding: 0.75rem 2rem; width: auto;">Reject
                                Request</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Confirm Action Modal -->
    <div id="confirmActionModal" class="modal-overlay" style="z-index: 10005;" onclick="closeConfirmModal()">
        <div class="modal-card" style="max-width: 400px; padding: 2rem; text-align: center;"
            onclick="event.stopPropagation()">
            <div id="confirmIconBox"
                style="width: 60px; height: 60px; border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center;">
                <i id="confirmIcon" style="width: 30px; height: 30px;"></i>
            </div>
            <h3 id="confirmTitle"
                style="font-family: 'Outfit'; font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">Confirm Action
            </h3>
            <p id="confirmMessage" style="color: #64748b; font-size: 0.95rem; margin-bottom: 2rem; line-height: 1.5;">Are
                you sure?</p>

            <div style="display: flex; gap: 1rem;">
                <button type="button" class="modal-btn" style="background: #f1f5f9; color: #475569; flex: 1;"
                    onclick="closeConfirmModal()">Cancel</button>
                <button type="button" id="confirmSubmitBtn" class="modal-btn" style="flex: 1;"
                    onclick="submitConfirmForm()">Yes</button>
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

        .stats-dashboard-grid-compact {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2.5rem;
            max-width: 600px; /* Constrain width for 2 compact cards */
        }

        .summary-card-compact {
            background: white;
            border-radius: 20px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .summary-card-compact:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .card-icon-box-compact {
            width: 3rem;
            height: 3rem;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .summary-card-compact.card-warning .card-icon-box-compact {
            background: #fffbeb;
            color: #f59e0b;
        }

        .summary-card-compact.card-success .card-icon-box-compact {
            background: #ecfdf5;
            color: #10b981;
        }

        .card-stats-compact {
            display: flex;
            flex-direction: column;
        }

        .stats-value-compact {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
            font-family: 'Outfit', sans-serif;
        }

        .stats-label-compact {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .action-bar-modern {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 1rem;
        }

        @media (max-width: 1024px) {
            .stats-dashboard-grid-compact {
                max-width: 100%;
            }
        }

        @media (max-width: 640px) {
            .stats-dashboard-grid-compact {
                grid-template-columns: 1fr;
            }

            .action-bar-modern {
                flex-direction: column;
                align-items: stretch;
            }

            .tab-switcher-container {
                width: 100%;
                display: flex;
            }

            .tab-switcher-container .tab-btn {
                flex: 1;
                text-align: center;
                padding: 0.85rem 1rem;
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
            color: #475569;
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
            transition: none !important;
            animation: none !important;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            cursor: pointer;
            border: 2px solid transparent;
        }

        .tr-row:hover {
            background: #fbfbff !important;
        }

        .tr-row:hover td {
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
        }

        /* NEW Highlighting Styles */
        .tr-row {
            position: relative;
        }

        .row-new-badge {
            position: absolute;
            top: -6px;
            left: -6px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: white;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 900;
            letter-spacing: 0.05em;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            z-index: 10;
            animation: pulse-blue 2s infinite;
            display: none;
        }

        .row-highlight-new {
            background: rgba(59, 130, 246, 0.08) !important;
            border-color: rgba(59, 130, 246, 0.3) !important;
        }

        @keyframes pulse-blue {
            0% {
                transform: scale(1);
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 4px 20px rgba(59, 130, 246, 0.6);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            }
        }

        @keyframes highlight-fade {
            from {
                background: rgba(59, 130, 246, 0.08);
            }

            to {
                background: white;
            }
        }

        body[data-theme="dark"] .row-highlight-new {
            background: rgba(245, 158, 11, 0.1) !important;
            border-color: rgba(245, 158, 11, 0.4) !important;
        }

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
            width: 12.5px;
            height: 12.5px;
            margin-top: -1px;
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
            transform: translateY(-4px);
            box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.4);
        }

        body[data-theme="dark"] .tr-row:hover td {
            border-top: 2px solid #8b5cf6 !important;
            border-bottom: 2px solid #8b5cf6 !important;
            color: white !important;
        }

        body[data-theme="dark"] .tr-row:hover td:first-child {
            border-left: 2px solid #8b5cf6 !important;
        }

        body[data-theme="dark"] .tr-row:hover td:last-child {
            border-right: 2px solid #8b5cf6 !important;
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
        body[data-theme="dark"] .modal-header,
        body[data-theme="dark"] .modal-footer {
            background: #1e293b;
            border-color: #334155;
        }

        body[data-theme="dark"] .empty-state-modern h3 {
            color: #f8fafc !important;
        }

        body[data-theme="dark"] .empty-state-modern p {
            color: #b3c4db !important;
        }

        body[data-theme="dark"] .empty-icon-wrapper {
            background: linear-gradient(135deg, #1e293b, #0f172a) !important;
            border-color: #334155 !important;
        }

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

        /* Layout Fix: Ensure Sidebar Alignment on Desktop */
        @media (min-width: 1024px) {
            #app-container:not(.collapsed-sidebar) .main-content {
                margin-left: 20rem !important;
            }

            .collapsed-sidebar .main-content {
                margin-left: 5.5rem !important;
            }
        }
    </style>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/requests.js') }}?v=1.4"></script>
@endpush