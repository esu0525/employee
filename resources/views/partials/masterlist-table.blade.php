<div class="master-list-grid">
    @if ($employees->count() > 0)
        @foreach ($employees as $employee)
        @php
            $displayName = $employee->name;
            if ($employee->last_name && $employee->first_name) {
                $displayName = $employee->last_name . ', ' . $employee->first_name;
                if ($employee->middle_name) {
                    $displayName .= ' ' . substr($employee->middle_name, 0, 1) . '.';
                }
            } else if (str_contains($employee->name, ',')) {
                $displayName = $employee->name;
            } else {
                $parts = explode(' ', trim($employee->name));
                if (count($parts) >= 2) {
                    $last = array_pop($parts);
                    $first = implode(' ', $parts);
                    $displayName = $last . ', ' . $first;
                }
            }
        @endphp

        <div class="master-item-card" onclick="window.location='{{ route('employees.show', ['id' => $employee->id]) }}'">
            <div class="master-card-left">
                <div class="master-avatar-wrapper">
                    <div class="master-avatar">
                        @if($employee->profile_picture)
                            <img src="{{ asset($employee->profile_picture) }}" alt="{{ $employee->name }}">
                        @else
                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                        @endif
                    </div>
                </div>
                <div class="master-info">
                    <span class="master-name">{{ $displayName }}</span>
                    <span class="master-sub" style="font-size: 0.85rem; opacity: 0.8; font-weight: 400;">{{ $employee->position }} • {{ $employee->department }}</span>
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

<div class="pagination-container" style="display: flex; justify-content: space-between; align-items: center;">
    <div class="pagination-info" style="font-size: 0.875rem; color: #64748b; font-weight: 500;">
        Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} results
    </div>
    <div class="pagination-links">
        {{ $employees->links('pagination::bootstrap-4') }}
    </div>
</div>
