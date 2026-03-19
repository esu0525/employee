<div class="master-list-grid">
    @if ($employees->count() > 0)
        @foreach ($employees as $employee)
        @php
            $lastName = $employee->last_name;
            $firstName = $employee->first_name;
            $middleName = $employee->middle_name;
            $suffix = $employee->suffix;

            if ($lastName && $firstName) {
                $miInitial = !empty($middleName) ? ' ' . strtoupper(substr($middleName, 0, 1)) . '.' : '';
                $displayName = $lastName . ', ' . $firstName . $miInitial;
                if ($suffix) $displayName .= ' ' . $suffix;
            } else {
                $displayName = $employee->name;
            }
        @endphp

        <div class="master-item-card" onclick="window.location='{{ route('employees.show', ['id' => $employee->id]) }}'">
            <div class="master-card-left">
                <div class="master-avatar-3layer">
                    <div class="master-avatar-ring">
                        <div class="master-avatar-inner">
                            @if($employee->profile_picture)
                                <img src="{{ asset($employee->profile_picture) }}" alt="{{ $employee->name }}">
                            @else
                                <div class="master-avatar-initials">
                                    {{ strtoupper(substr($employee->last_name ?? $employee->name, 0, 1)) }}{{ strtoupper(substr($employee->first_name ?? '', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="master-info">
                    <span class="master-name">{{ $displayName }}</span>
                    <span class="master-sub" style="font-size: 0.85rem; opacity: 0.8; font-weight: 400;">{{ $employee->position }} • {{ $employee->agency }}</span>
                </div>
            </div>
            
            <div class="master-card-right">
                <button type="button" class="btn-update-status" 
                        onclick="event.stopPropagation(); openStatusModal('{{ $employee->id }}', '{{ $displayName }}', '{{ $employee->status }}')" 
                        title="Update Status">
                    <i data-lucide="refresh-cw"></i>
                    <span>Status</span>
                </button>
                <i data-lucide="chevron-right" class="master-action-arrow"></i>
            </div>
        </div>
        @endforeach
    @else
        <div class="empty-state">
            <i data-lucide="search" class="empty-icon" style="width: 48px; height: 48px;"></i>
            <p class="empty-title">No employees found matching your search criteria</p>
            <p class="empty-subtitle">Try adjusting your search terms</p>
        </div>
    @endif
</div>

{{-- Modern Pagination Footer --}}
<div class="pagination-footer animate-up" style="--delay: 0.1s; margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; background: var(--bg-card); padding: 1rem 1.5rem; border-radius: 20px; border: 1px solid var(--border); box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);">
    <div class="pagination-info" style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">
        Showing <span style="color: var(--primary);">{{ $employees->firstItem() ?? 0 }}</span> - <span style="color: var(--primary);">{{ $employees->lastItem() ?? 0 }}</span> of <span style="color: var(--text-main);">{{ $employees->total() }}</span>
    </div>
    
    <div class="pagination-actions" style="display: flex; gap: 0.75rem; align-items: center;">
        @if ($employees->onFirstPage())
            <button class="btn-nav disabled" disabled title="Previous Page">
                <i data-lucide="chevron-left"></i>
            </button>
        @else
            <a href="javascript:void(0)" class="btn-nav btn-nav-active pagination-ajax" data-page="{{ $employees->currentPage() - 1 }}" title="Previous Page">
                <i data-lucide="chevron-left"></i>
            </a>
        @endif

        <div style="display: flex; align-items: center; gap: 0.6rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; background: var(--bg-main); padding: 0.25rem 0.75rem; border-radius: 12px; border: 1px solid var(--border);">
            <input type="number" id="pageNumberInput" value="{{ $employees->currentPage() }}" min="1" max="{{ $employees->lastPage() }}"
                   style="width: 42px; text-align: center; border: none; background: transparent; color: var(--primary); font-weight: 800; font-size: 0.95rem; outline: none; padding: 0;"
                   onkeydown="if(event.key === 'Enter') { 
                       let page = parseInt(this.value); 
                       if(page >= 1 && page <= {{ $employees->lastPage() }}) { 
                           fetchData(page); 
                       } else {
                           this.value = {{ $employees->currentPage() }};
                       }
                   }">
            <span style="opacity: 0.6;">of</span>
            <span style="color: var(--text-main); font-weight: 800;">{{ $employees->lastPage() }}</span>
        </div>

        @if ($employees->hasMorePages())
            <a href="javascript:void(0)" class="btn-nav btn-nav-active pagination-ajax" data-page="{{ $employees->currentPage() + 1 }}" title="Next Page">
                <i data-lucide="chevron-right"></i>
            </a>
        @else
            <button class="btn-nav disabled" disabled title="Next Page">
                <i data-lucide="chevron-right"></i>
            </button>
        @endif
    </div>
</div>
