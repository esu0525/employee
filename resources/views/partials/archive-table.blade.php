@php
    $hasDetails = ($type === 'retired' || $type === 'transfer');
    $hasSoNo    = ($type === 'transfer');
@endphp

<div class="table-container shadow-sm">
    <div class="table-wrapper">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Separation Date</th>
                    <th>Agency/Station</th>
                    @if($hasDetails)<th>Details</th>@endif
                    @if($hasSoNo)<th>S.O. Number</th>@endif
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                <tr class="hover-row" onclick="window.location='{{ route('employees.show', ['id' => $employee->id]) }}'">
                    <td>
                        <div class="user-info-cell">
                            <div class="user-avatar-small" style="overflow: hidden; padding: 0;">
                                @if($employee->profile_picture_content)
                                    <img src="{{ route('display.employee-avatar', ['id' => $employee->id]) }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                @elseif($employee->profile_picture)
                                    <img src="{{ asset($employee->profile_picture) }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="user-details">
                                @php
                                    $miInitial = !empty($employee->middle_name) ? ' ' . strtoupper(substr($employee->middle_name, 0, 1)) . '.' : '';
                                    $archiveDisplayName = $employee->last_name . ', ' . $employee->first_name . $miInitial;
                                    if ($employee->suffix) $archiveDisplayName .= ' ' . $employee->suffix;
                                @endphp
                                <span class="user-name">{{ $archiveDisplayName }}</span>
                                <span class="user-sub">{{ $employee->position }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $sepDate = $employee->effective_date ?: $employee->status_date;
                        @endphp
                        <span class="date-text">{{ $sepDate ? $sepDate->format('M d, Y') : '-' }}</span>
                    </td>
                    <td>
                        <span class="agency-text">{{ $employee->agency ?: $employee->school ?: '-' }}</span>
                    </td>
                    @if($hasDetails)
                    <td>
                        @if ($employee->status === 'transfer')
                            @php
                                $destination = $employee->status_specify ?: ($employee->transfer_to ?: ($employee->transfer_location ?: null));
                            @endphp
                            <div class="detail-badge info">
                                <i data-lucide="map-pin"></i>
                                <span>{{ $destination ?: 'N/A' }}</span>
                            </div>
                        @elseif ($employee->status === 'retired')
                            <div class="detail-badge warning">
                                <i data-lucide="award"></i>
                                @php
                                    $ru = $employee->retirement_under ?: $employee->status_specify ?: 'N/A';
                                    if ($ru !== 'N/A' && !Str::contains(strtolower($ru), 'retirement under') && !Str::contains(strtolower($ru), 'separation')) {
                                        $ru = 'Retirement under ' . (Str::contains(strtoupper($ru), 'R.A') ? '' : 'R.A ') . $ru;
                                    }
                                @endphp
                                <span>{{ $ru }}</span>
                            </div>
                        @endif
                    </td>
                    @endif
                    @if($hasSoNo)
                    <td>
                        @if($employee->so_no)
                            <span class="so-badge">{{ $employee->so_no }}</span>
                        @else
                            <span class="text-muted-sm">—</span>
                        @endif
                    </td>
                    @endif
                    <td>
                        @if($type === 'others')
                            <span class="badge {{ $badge }}">
                                {{ strtoupper($employee->status_specify ?: $label) }}
                            </span>
                        @else
                            <span class="badge {{ $badge }}">
                                {{ strtoupper($label) }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="{{ $hasDetails ? ($hasSoNo ? 6 : 5) : 4 }}" class="empty-cell">
                        <div class="empty-state">
                            <i data-lucide="{{ $icon }}"></i>
                            <p>No archived {{ strtolower($label) }} records found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .user-info-cell { display: flex; align-items: center; gap: 1rem; }
    .user-avatar-small {
        width: 2.5rem; height: 2.5rem; border-radius: 12px;
        background: var(--primary-soft); color: var(--primary);
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.85rem; border: 1px solid var(--primary-soft);
    }
    .user-details { display: flex; flex-direction: column; }
    .user-name { font-weight: 700; color: var(--text-main); font-size: 0.9rem; }
    .user-sub { font-size: 0.75rem; color: var(--text-muted); font-weight: 600; }
    
    .date-text { font-weight: 600; color: var(--text-main); font-size: 0.85rem; }
    .agency-text { color: var(--text-muted); font-size: 0.85rem; font-weight: 500; }

    .detail-badge {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.4rem 0.75rem; border-radius: 8px; font-weight: 700; font-size: 0.75rem;
    }
    .detail-badge.info { background: var(--info-soft); color: var(--info); }
    .detail-badge.warning { background: var(--warning-soft); color: var(--warning); }
    .detail-badge i { width: 14px; height: 14px; }

    .empty-cell { padding: 4rem 2rem !important; text-align: center; }
    .empty-state { display: flex; flex-direction: column; align-items: center; gap: 1rem; color: var(--text-muted); }
    .empty-state i { width: 3rem; height: 3rem; opacity: 0.2; }
    .empty-state p { font-weight: 600; font-size: 0.9rem; }

    .hover-row { cursor: pointer; transition: all 0.2s; border-left: 3px solid transparent; }
    .hover-row:hover { 
        background: rgba(79, 70, 229, 0.04) !important; 
        transform: translateX(4px); 
        border-left-color: var(--primary);
    }

    .so-badge {
        display: inline-block;
        background: var(--primary-soft);
        color: var(--primary);
        border: 1px solid rgba(79,70,229,0.15);
        border-radius: 7px;
        padding: 0.2rem 0.55rem;
        font-size: 0.72rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .text-muted-sm { color: var(--text-muted); font-size: 1rem; }
</style>
