<div class="card-premium" style="background: var(--bg-card); border-radius: 20px; overflow: hidden; border: 1px solid var(--border-light); box-shadow: var(--shadow-sm);">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: var(--bg-main); border-bottom: 2px solid var(--border-light);">
                    <th style="padding: 1.25rem 1.5rem; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.1em; width: 20%;">User</th>
                    <th style="padding: 1.25rem 1.5rem; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.1em; width: 20%;">Action</th>
                    <th style="padding: 1.25rem 1.5rem; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.1em; width: 20%;">Module</th>
                    <th style="padding: 1.25rem 1.5rem; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.1em; width: 20%;">Activity Details</th>
                    <th style="padding: 1.25rem 1.5rem; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.1em; width: 20%;">Date & Time</th>
                </tr>
            </thead>
            <tbody style="font-family: 'Inter', sans-serif;">
                @forelse($logs as $log)
                <tr style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.25rem 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <div style="width: 40px; height: 40px; border-radius: 12px; background: {{ $log->user ? ($log->user->role === 'admin' ? '#f5f3ff' : '#eff6ff') : '#f8fafc' }}; color: {{ $log->user ? ($log->user->role === 'admin' ? '#8b5cf6' : '#3b82f6') : '#94a3b8' }}; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; flex-shrink: 0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                                {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}
                            </div>
                            <div style="display: flex; flex-direction: column; min-width: 0;">
                                <span style="font-weight: 700; color: var(--text-main); font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $log->user ? $log->user->name : 'System/Deleted' }}</span>
                                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">{{ $log->ip_address }}</span>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1.25rem 1.5rem;">
                        @php
                            $actionColors = [
                                'create' => ['#10b981', '#ecfdf5'],
                                'edit' => ['#3b82f6', '#eff6ff'],
                                'delete' => ['#ef4444', '#fef2f2'],
                                'login' => ['#8b5cf6', '#f5f3ff'],
                                'upload' => ['#f59e0b', '#fffbeb'],
                                'export' => ['#06b6d4', '#ecfeff'],
                                'view' => ['#6366f1', '#eef2ff']
                            ];
                            $colors = $actionColors[strtolower($log->action)] ?? ['#64748b', '#f1f5f9'];
                        @endphp
                        <span style="display: inline-flex; align-items: center; padding: 0.35rem 0.75rem; border-radius: 8px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: {{ $colors[0] }}; background: {{ $colors[1] }};">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td style="padding: 1.25rem 1.5rem;">
                        @php
                            $mod = strtolower($log->module);
                            $moduleInfo = match(true) {
                                str_contains($mod, 'masterlist') => ['color' => '#3b82f6', 'icon' => 'users'],
                                str_contains($mod, 'archive')    => ['color' => '#f59e0b', 'icon' => 'archive'],
                                str_contains($mod, 'request')    => ['color' => '#10b981', 'icon' => 'file-stack'],
                                str_contains($mod, 'auth') || str_contains($mod, 'login') => ['color' => '#8b5cf6', 'icon' => 'shield-check'],
                                str_contains($mod, 'account')    => ['color' => '#6366f1', 'icon' => 'settings-2'],
                                str_contains($mod, 'profile')    => ['color' => '#ec4899', 'icon' => 'user-circle'],
                                str_contains($mod, 'audit')      => ['color' => '#475569', 'icon' => 'clipboard-list'],
                                default                          => ['color' => '#64748b', 'icon' => 'activity']
                            };
                        @endphp
                        <div style="display: flex; align-items: center; gap: 0.5rem; color: {{ $moduleInfo['color'] }}; font-weight: 700; font-size: 0.85rem;">
                            <i data-lucide="{{ $moduleInfo['icon'] }}" style="width: 16px; height: 16px;"></i>
                            <span style="text-transform: capitalize;">{{ $log->module }}</span>
                        </div>
                    </td>
                    <td style="padding: 1.25rem 1.5rem;">
                        <p style="margin: 0; font-size: 0.9rem; color: var(--text-main); font-weight: 500; line-height: 1.5;">{{ $log->description }}</p>
                    </td>
                    <td style="padding: 1.25rem 1.5rem;">
                        <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                            <span style="font-weight: 700; color: var(--text-main); font-size: 0.85rem;">{{ $log->created_at->format('M d, Y') }}</span>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; display: flex; align-items: center; gap: 0.35rem;">
                                <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                                {{ $log->created_at->format('h:i A') }}
                            </span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 5rem 2rem; text-align: center;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem; color: var(--text-muted);">
                            <i data-lucide="clipboard-list" style="width: 4rem; height: 4rem; opacity: 0.15;"></i>
                            <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0;">No logs found</h3>
                            <p style="margin: 0; font-size: 0.9rem;">Try adjusting your filters or search terms.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($logs->hasPages())
<div class="pagination-footer" style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; background: var(--bg-card); padding: 1rem 1.5rem; border-radius: 20px; border: 1px solid var(--border-light); box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);">
    <div class="pagination-info" style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">
        Showing <span style="color: var(--primary);">{{ $logs->firstItem() ?? 0 }}</span> - <span style="color: var(--primary);">{{ $logs->lastItem() ?? 0 }}</span> of <span style="color: var(--text-main);">{{ $logs->total() }}</span>
    </div>
    
    <div class="pagination-actions" style="display: flex; gap: 0.75rem; align-items: center;">
        @if ($logs->onFirstPage())
            <button class="btn-nav disabled" disabled title="Previous Page" style="display: inline-flex; align-items: center; gap: 0.625rem; padding: 0.75rem 1.25rem; border-radius: 14px; font-size: 0.875rem; font-weight: 700; border: 1px solid var(--border-light); background: var(--bg-main); color: var(--text-muted); opacity: 0.4; cursor: not-allowed;">
                <i data-lucide="chevron-left" style="width: 1.125rem; height: 1.125rem;"></i>
            </button>
        @else
            <a href="javascript:void(0)" class="btn-nav btn-nav-active pagination-ajax" data-page="{{ $logs->currentPage() - 1 }}" title="Previous Page" style="display: inline-flex; align-items: center; gap: 0.625rem; padding: 0.75rem 1.25rem; border-radius: 14px; font-size: 0.875rem; font-weight: 700; border: 1px solid var(--border-light); background: var(--bg-main); color: var(--text-muted); cursor: pointer; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='var(--primary)'; this.style.color='white'" onmouseout="this.style.background='var(--bg-main)'; this.style.color='var(--text-muted)'">
                <i data-lucide="chevron-left" style="width: 1.125rem; height: 1.125rem;"></i>
            </a>
        @endif

        <div style="display: flex; align-items: center; gap: 0.6rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; background: var(--bg-main); padding: 0.25rem 0.75rem; border-radius: 12px; border: 1px solid var(--border-light);">
            <input type="number" value="{{ $logs->currentPage() }}" min="1" max="{{ $logs->lastPage() }}"
                   style="width: 42px; text-align: center; border: none; background: transparent; color: var(--primary); font-weight: 800; font-size: 0.95rem; outline: none; padding: 0;"
                   onkeydown="if(event.key === 'Enter') { 
                       let page = parseInt(this.value); 
                       if(page >= 1 && page <= {{ $logs->lastPage() }}) { 
                           filterLogs(page); 
                       } else {
                           this.value = {{ $logs->currentPage() }};
                       }
                   }">
            <span style="opacity: 0.6;">of</span>
            <span style="color: var(--text-main); font-weight: 800;">{{ $logs->lastPage() }}</span>
        </div>

        @if ($logs->hasMorePages())
            <a href="javascript:void(0)" class="btn-nav btn-nav-active pagination-ajax" data-page="{{ $logs->currentPage() + 1 }}" title="Next Page" style="display: inline-flex; align-items: center; gap: 0.625rem; padding: 0.75rem 1.25rem; border-radius: 14px; font-size: 0.875rem; font-weight: 700; border: 1px solid var(--border-light); background: var(--bg-main); color: var(--text-muted); cursor: pointer; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='var(--primary)'; this.style.color='white'" onmouseout="this.style.background='var(--bg-main)'; this.style.color='var(--text-muted)'">
                <i data-lucide="chevron-right" style="width: 1.125rem; height: 1.125rem;"></i>
            </a>
        @else
            <button class="btn-nav disabled" disabled title="Next Page" style="display: inline-flex; align-items: center; gap: 0.625rem; padding: 0.75rem 1.25rem; border-radius: 14px; font-size: 0.875rem; font-weight: 700; border: 1px solid var(--border-light); background: var(--bg-main); color: var(--text-muted); opacity: 0.4; cursor: not-allowed;">
                <i data-lucide="chevron-right" style="width: 1.125rem; height: 1.125rem;"></i>
            </button>
        @endif
    </div>
</div>
@endif

