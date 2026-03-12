<div class="action-bar" style="margin-bottom: 1rem; border-bottom: none;">
    <div style="flex: 1;"></div>
    <div class="stat-meta" style="background: white; border: 1px solid var(--border); padding: 0.75rem 1.5rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        <i data-lucide="{{ $icon }}" style="color: var(--text-muted);"></i>
        <span style="font-weight: 700; color: var(--text-main);">{{ $employees->count() }}</span>
        <span style="color: var(--text-muted); font-size: 0.875rem;">Total {{ $label }}</span>
    </div>
</div>

<div class="table-container">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    @if($type === 'transfer')
                        <th>Effective Date</th>
                        <th>School</th>
                        <th>Transfer To</th>
                        <th>SO No.</th>
                    @else
                        <th>Position</th>
                        <th>Department</th>
                        <th>Date Joined</th>
                        <th>Date</th>
                    @endif
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if ($employees->count() > 0)
                    @foreach ($employees as $employee)
                    <tr>
                        <td class="employee-id" style="color: #6b7280;">{{ $employee->id }}</td>
                        <td style="font-weight: 500;">
                            <a href="{{ route('employees.show', ['id' => $employee->id]) }}" class="employee-name-link">
                                {{ $employee->name }}
                            </a>
                        </td>
                        @if($type === 'transfer')
                            <td>{{ $employee->effective_date ? $employee->effective_date->format('M d, Y') : '-' }}</td>
                            <td>{{ $employee->school ?: '-' }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i data-lucide="map-pin" style="width: 14px; height: 14px; color: var(--info);"></i>
                                    <span style="font-weight: 500;">
                                        {{ $employee->transfer_to ?: '-' }}
                                    </span>
                                </div>
                            </td>
                            <td>{{ $employee->so_no ?: '-' }}</td>
                        @else
                            <td>{{ $employee->position }}</td>
                            <td>
                                <span class="badge badge-outline badge-outline-gray">
                                    {{ $employee->department }}
                                </span>
                            </td>
                            <td>{{ $employee->date_joined ? $employee->date_joined->format('M d, Y') : '-' }}</td>
                            <td>
                                {{ $employee->status_date ? $employee->status_date->format('M d, Y') : '-' }}
                            </td>
                        @endif
                        <td>
                            <span class="badge {{ $badge }}">{{ $label }}</span>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ $type === 'transfer' ? '8' : '7' }}">
                            <div class="empty-state">
                                <i data-lucide="{{ $icon }}" class="empty-icon" style="width: 48px; height: 48px;"></i>
                                <p class="empty-title">No {{ strtolower($label) }} employees found</p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
