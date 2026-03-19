@extends('layouts.app')

@section('title', 'Request List')

@section('content')
<div class="page-content">
    <div class="page-header" style="margin-bottom: 1rem;">
        <h1 class="page-title">Request List</h1>
        <p class="page-subtitle">Pending document requests</p>
    </div>

    <!-- Action Bar -->
    <div class="action-bar" style="margin-bottom: 1rem;">
        <div class="search-container">
            <i data-lucide="search" class="search-icon"></i>
            <form method="GET" action="{{ route('employees.requests') }}" id="searchForm">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search by name, document type..."
                    value="{{ $search }}"
                    onchange="document.getElementById('searchForm').submit()"
                >
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container" style="margin-top: 0;">
        <div class="table-wrapper">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                <thead>
                    <tr>
                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border); width: 40px;">ID</th>
                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border);">Employee / Agency</th>
                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border);">Document / Purpose</th>
                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border); width: 60px;">Copies</th>
                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border); width: 100px;">Date</th>
                        <th style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border); width: 120px;">Files</th>
                        <th style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border); width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($requests->count() > 0)
                        @foreach ($requests as $req)
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 0.5rem 0.75rem; vertical-align: middle;">
                                <span style="color: var(--primary); font-weight: 600; font-size: 0.75rem;">{{ $req->id }}</span>
                            </td>
                            <td style="padding: 0.5rem 0.75rem; vertical-align: middle;">
                                <a href="{{ route('portal.view', ['id' => $req->id]) }}" style="text-decoration: none;">
                                    <span style="font-weight: 600; color: var(--text-main); font-size: 0.8125rem; border-bottom: 1px dashed var(--primary);">{{ $req->employee_name }}</span>
                                </a>
                                <br>
                                <span style="font-size: 0.6875rem; color: var(--text-muted);">{{ $req->agency ?? $req->employee_id }}</span>
                            </td>
                            <td style="padding: 0.5rem 0.75rem; vertical-align: middle;">
                                <span style="display: block; font-weight: 600; font-size: 0.75rem; color: var(--primary);">{{ $req->request_type }}</span>
                                <span style="font-size: 0.6875rem; color: var(--text-muted);">Purpose: {{ $req->purpose ?? 'N/A' }}</span>
                            </td>
                            <td style="padding: 0.5rem 0.75rem; vertical-align: middle; text-align: center;">
                                <span style="font-weight: 700; font-size: 0.8125rem;">{{ $req->num_copies ?? 1 }}</span>
                            </td>
                            <td style="padding: 0.5rem 0.75rem; vertical-align: middle; white-space: nowrap; font-size: 0.8125rem; color: var(--text-main);">
                                {{ $req->request_date->format('M d, Y') }}
                            </td>
                            <td style="padding: 0.5rem 0.75rem; vertical-align: middle; text-align: center;">
                                @if($req->requirements_file)
                                    <a href="{{ asset($req->requirements_file) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: #f1f5f9; border: 1px solid var(--border); border-radius: 6px; color: var(--primary); text-decoration: none; font-size: 0.6875rem; font-weight: 600;">
                                        <i data-lucide="paperclip" style="width: 12px; height: 12px;"></i>
                                        View ID
                                    </a>
                                @else
                                    <span style="font-size: 0.6875rem; color: var(--text-muted); font-style: italic;">No file</span>
                                @endif
                            </td>
                            <td style="padding: 0.5rem 0.75rem; vertical-align: middle; text-align: center;">
                                <div style="display: flex; gap: 0.375rem; justify-content: center;">
                                     <form action="{{ route('requests.approve', $req->id) }}" method="POST" style="display: inline;">
                                         @csrf
                                         <button type="submit" style="display: inline-flex; align-items: center; gap: 0.2rem; padding: 0.3rem 0.625rem; border-radius: 6px; font-size: 0.6875rem; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.3); background: rgba(16, 185, 129, 0.08); color: #059669; cursor: pointer; transition: all 0.2s ease; white-space: nowrap;" onmouseover="this.style.background='#10b981'; this.style.color='white'; this.style.borderColor='#10b981';" onmouseout="this.style.background='rgba(16, 185, 129, 0.08)'; this.style.color='#059669'; this.style.borderColor='rgba(16, 185, 129, 0.3)';">
                                             <i data-lucide="check" style="width: 12px; height: 12px;"></i>
                                             Approve
                                         </button>
                                     </form>
                                     <form action="{{ route('requests.reject', $req->id) }}" method="POST" style="display: inline;">
                                         @csrf
                                         <button type="submit" style="display: inline-flex; align-items: center; gap: 0.2rem; padding: 0.3rem 0.625rem; border-radius: 6px; font-size: 0.6875rem; font-weight: 600; border: 1px solid rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.08); color: #dc2626; cursor: pointer; transition: all 0.2s ease; white-space: nowrap;" onmouseover="this.style.background='#ef4444'; this.style.color='white'; this.style.borderColor='#ef4444';" onmouseout="this.style.background='rgba(239, 68, 68, 0.08)'; this.style.color='#dc2626'; this.style.borderColor='rgba(239, 68, 68, 0.3)';">
                                             <i data-lucide="x" style="width: 12px; height: 12px;"></i>
                                             Reject
                                         </button>
                                     </form>
                                 </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i data-lucide="check-circle" class="empty-icon" style="width: 48px; height: 48px; color: var(--success);"></i>
                                    <p class="empty-title">All caught up!</p>
                                    <p class="empty-subtitle">No pending requests to review</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Success Toast -->
@if(session('success_message'))
<div id="successToast" style="position: fixed; top: 2rem; right: 2rem; z-index: 9999; display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: 12px; box-shadow: 0 20px 60px -15px rgba(0,0,0,0.25); animation: toastSlideIn 0.5s cubic-bezier(0.34,1.56,0.64,1); max-width: 400px; background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #6ee7b7; color: #065f46;">
    <div style="display: flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background: #10b981; color: white; border-radius: 8px; flex-shrink: 0;">
        <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
    </div>
    <div style="flex: 1;">
        <p style="font-weight: 700; font-size: 0.8125rem;">Success!</p>
        <p style="font-size: 0.75rem; color: #047857;">{{ session('success_message') }}</p>
    </div>
    <button onclick="closeToast()" style="background: none; border: none; color: #6ee7b7; cursor: pointer; padding: 0.25rem;">
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
document.addEventListener('DOMContentLoaded', function() {
    const toast = document.getElementById('successToast');
    if (toast) setTimeout(() => closeToast(), 4000);
});
</script>
@endpush
