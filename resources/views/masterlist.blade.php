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
    .modal { display: none; position: fixed; inset: 0; z-index: 2000; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1.5rem; }
    .modal.active { display: flex; animation: fadeIn 0.3s ease-out; }
    .modal-content { background: white; border-radius: 20px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; }
    .modal-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
    .modal-title { font-size: 1rem; font-weight: 800; color: #1e293b; margin: 0; }
    .modal-body { padding: 1.5rem; }
    .modal-footer { padding: 1.25rem 1.5rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 0.75rem; background: #f8fafc; }
    
    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.5rem; text-transform: uppercase; }
    .form-input { width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; transition: 0.2s; }
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

    // Initial attachment
    document.addEventListener('DOMContentLoaded', () => {
        attachPaginationLinks();
        
        // Auto-dismiss toast
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
