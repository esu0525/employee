@php
    $total = $employees->count();
    $perPage = 10;
    $lastPage = ceil($total / $perPage);
    if ($lastPage < 1) $lastPage = 1;
@endphp
<div class="pagination-footer-client" id="pagination-{{ $tab }}"
    style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; background: var(--bg-card); padding: 0.75rem 1.25rem; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 12px -2px rgba(0,0,0,0.05);">
    <div class="pagination-info" style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">
        Showing <span class="first-item" style="color: var(--primary);">{{ $total > 0 ? 1 : 0 }}</span> - <span class="last-item" style="color: var(--primary);">{{ min($perPage, $total) }}</span> of <span class="total-items" style="color: var(--text-main);">{{ $total }}</span>
    </div>
    
    <div class="pagination-actions" style="display: flex; gap: 0.5rem; align-items: center;">
        <button onclick="changePage('{{ $tab }}', -1)" class="btn-nav btn-prev" style="padding: 0.5rem; border-radius: 10px; border: 1px solid var(--border); background: var(--bg-card); color: var(--text-main); display: flex; align-items: center; transition: all 0.2s;">
            <i data-lucide="chevron-left" style="width: 16px; height: 16px;"></i>
        </button>

        <div style="display: flex; align-items: center; gap: 0.6rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; background: var(--bg-main); padding: 0.25rem 0.75rem; border-radius: 12px; border: 1px solid var(--border);">
            <input type="number" 
                   value="1" 
                   min="1" 
                   max="{{ $lastPage }}"
                   class="page-input"
                   onchange="goToPage('{{ $tab }}', this.value)"
                   style="width: 42px; text-align: center; border: none; background: transparent; color: var(--primary); font-weight: 800; font-size: 0.95rem; outline: none; padding: 0;">
            <span style="opacity: 0.6;">of</span>
            <span class="last-page" style="color: var(--text-main); font-weight: 800;">{{ $lastPage }}</span>
        </div>

        <button onclick="changePage('{{ $tab }}', 1)" class="btn-nav btn-next" style="padding: 0.5rem; border-radius: 10px; border: 1px solid var(--border); background: var(--bg-card); color: var(--text-main); display: flex; align-items: center; transition: all 0.2s;">
            <i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i>
        </button>
    </div>
</div>
