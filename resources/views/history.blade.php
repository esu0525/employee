@extends('layouts.app')

@section('title', 'History Dashboard')

@section('content')
<div class="page-content" style="padding: 1.5rem; max-width: 1600px; margin: 0 auto;">
    <div class="page-header">
        <h1 class="page-title">History</h1>
        <p class="page-subtitle">View employee history records by category</p>
    </div>

    <!-- Action Bar -->
    <div class="action-bar" style="margin-bottom: 2rem;">
        <div class="search-container" style="flex: 1; max-width: 600px;">
            <i data-lucide="search" class="search-icon"></i>
            <form method="GET" action="{{ route('employees.history') }}" id="searchForm">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search historical records..."
                    value="{{ $search }}"
                    onchange="document.getElementById('searchForm').submit()"
                >
            </form>
        </div>
    </div>

    <!-- Tabbed Navigation -->
    <div class="profile-tabs-nav animate-slide-up" style="animation-delay: 0.1s;">
        <button class="tab-btn active" onclick="switchTab('resign', this)">Resigned</button>
        <button class="tab-btn" onclick="switchTab('retired', this)">Retired</button>
        <button class="tab-btn" onclick="switchTab('transfer', this)">Transferred</button>
    </div>

    <div class="main-profile-layout">
        <div class="info-content-area full-width animate-slide-up" style="animation-delay: 0.2s;">
            
            <!-- Resigned Tab -->
            <div id="resignTab" class="info-tab-pane active">
                @include('partials.history-table', ['employees' => $resign, 'type' => 'resign', 'icon' => 'user-minus', 'badge' => 'badge-resign', 'label' => 'Resigned'])
            </div>

            <!-- Retired Tab -->
            <div id="retiredTab" class="info-tab-pane">
                @include('partials.history-table', ['employees' => $retired, 'type' => 'retired', 'icon' => 'award', 'badge' => 'badge-retired', 'label' => 'Retired'])
            </div>

            <!-- Transferred Tab -->
            <div id="transferTab" class="info-tab-pane">
                @include('partials.history-table', ['employees' => $transfer, 'type' => 'transfer', 'icon' => 'arrow-right-left', 'badge' => 'badge-transfer', 'label' => 'Transferred'])
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
    /* Tabs Bar (Shared from profile design) */
    .profile-tabs-nav { 
        display: flex; gap: 0.5rem; 
        margin-bottom: 1.5rem; 
        border-bottom: 2px solid var(--border-light); 
        padding: 0 0.5rem;
    }
    .tab-btn { 
        background: none; border: none; 
        padding: 0.85rem 1.5rem; 
        font-size: 0.9rem; font-weight: 700; 
        color: var(--text-muted); cursor: pointer; 
        position: relative; transition: 0.3s; 
        border-radius: 10px 10px 0 0;
    }
    .tab-btn:hover { color: var(--text-main); background: var(--border-light); }
    .tab-btn.active { color: var(--primary); }
    .tab-btn.active::after { 
        content: ''; position: absolute; bottom: -2px; left: 0; right: 0; 
        height: 3px; background: var(--primary); border-radius: 10px; 
    }

    .info-tab-pane { display: none; }
    .info-tab-pane.active { display: block; animation: fadeIn 0.4s ease-out; }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .animate-slide-up { animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    
    .table-container { margin-top: 0; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if(typeof lucide !== 'undefined') lucide.createIcons();
        
        // Restore active tab from localStorage if available
        const activeTabId = localStorage.getItem('historyActiveTab') || 'resign';
        switchTab(activeTabId, document.querySelector(`.tab-btn[onclick*="${activeTabId}"]`));
    });

    function switchTab(tabId, btnElement) {
        if(!btnElement) {
            btnElement = document.querySelector(`.tab-btn[onclick*="${tabId}"]`);
        }
        
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.info-tab-pane').forEach(pane => pane.classList.remove('active'));
        
        if(btnElement) btnElement.classList.add('active');
        
        const targetPane = document.getElementById(tabId + 'Tab');
        if(targetPane) targetPane.classList.add('active');

        // Save active tab state
        localStorage.setItem('historyActiveTab', tabId);
    }
</script>
@endpush
@endsection
