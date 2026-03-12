<div class="table-wrapper">
    <table class="compact-table">
        <thead>
            <tr>
                @if($sort === 'box')
                <th style="width: 100px;">Box</th>
                @endif
                <th>Employee Name</th>
            </tr>
        </thead>
        <tbody>
            @if ($employees->count() > 0)
                @php $lastBox = null; @endphp
                @foreach ($employees as $employee)
                @php
                    $currentBox = $employee->box_number ?? 'N/A';
                    
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

                @if($sort === 'box' && $currentBox !== $lastBox)
                @php
                    // Filter out any "TOTAL" rows that might have been imported
                    if (str_contains(strtoupper($currentBox), 'TOTAL')) continue;

                    $parts = explode('|', $currentBox);
                    $boxId = $parts[0] ?? $currentBox;
                    $boxDesc = $parts[1] ?? '';
                @endphp
                <tr class="box-header-row" style="background: #f1f5f9; font-weight: 700; border-top: 2px solid var(--border-color);">
                    <td colspan="2" style="padding: 1rem 1.25rem; border-left: 5px solid var(--primary-color);">
                        <div style="font-size: 1.15rem; color: var(--text-main); font-family: 'Outfit', sans-serif;">BOX {{ strtoupper($boxId) }}</div>
                        @if($boxDesc)
                        <div style="font-size: 0.875rem; color: var(--primary-color); font-weight: 600; margin-top: 0.125rem;">{{ strtoupper($boxDesc) }}</div>
                        @endif
                    </td>
                </tr>
                @php $lastBox = $currentBox; @endphp
                @endif

                @php
                    // Skip the row if it's a TOTAL record
                    if (str_contains(strtoupper($currentBox), 'TOTAL')) continue;
                @endphp

                <tr onclick="window.location='{{ route('employees.show', ['id' => $employee->id]) }}'" style="cursor: pointer;" class="employee-row-clickable">
                    @if($sort === 'box')
                    <td>
                        <span class="badge badge-outline badge-outline-indigo">
                            {{ explode('|', $currentBox)[0] }}
                        </span>
                    </td>
                    @endif
                    <td>
                        <div class="employee-cell">
                            <div class="employee-avatar">
                                {{ strtoupper(substr($employee->last_name ?? $employee->name, 0, 1)) }}
                            </div>
                            <div class="employee-info-main">
                                <a href="{{ route('employees.show', ['id' => $employee->id]) }}" class="employee-name-link" style="text-decoration: none; color: inherit;">
                                    <span class="employee-name-display" style="font-weight: 500; font-family: 'Cambria', serif; font-size: 1rem;">
                                        {{ strtoupper($displayName) }}
                                    </span>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="{{ $sort === 'box' ? 2 : 1 }}">
                        <div class="empty-state">
                            <i data-lucide="search" class="empty-icon" style="width: 48px; height: 48px;"></i>
                            <p class="empty-title">No employees found matching your search criteria</p>
                            <p class="empty-subtitle">Try adjusting your search terms</p>
                        </div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div class="pagination-container" style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
    <div class="pagination-info" style="font-size: 0.875rem; color: var(--text-muted);">
        Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} results
    </div>
    <div class="pagination-links">
        {{ $employees->links('pagination::bootstrap-4') }}
    </div>
</div>
