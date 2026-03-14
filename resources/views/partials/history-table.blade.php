<div class="action-bar" style="margin-bottom: 1rem; border-bottom: none;">
    <div style="flex: 1;"></div>
    <div class="stat-meta" style="background: var(--bg-card); border: 1px solid var(--border); padding: 0.75rem 1.5rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
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
                    <th>Name</th>
                    <th>Date of Separation</th>
                    <th>School</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if ($employees->count() > 0)
                    @foreach ($employees as $employee)
                    <tr>
                        <td style="font-weight: 500;">
                            <a href="{{ route('employees.show', ['id' => $employee->id]) }}" class="employee-name-link">
                                {{ $employee->name }}
                            </a>
                        </td>
                        <td>
                            @php
                                $sepDate = $employee->effective_date ?: $employee->status_date;
                            @endphp
                            {{ $sepDate ? $sepDate->format('M d, Y') : '-' }}
                        </td>
                        <td>
                            {{ $employee->school ?: $employee->department ?: '-' }}
                        </td>
                        <td>
                            <span class="badge {{ $badge }}">{{ $label }}</span>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">
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
