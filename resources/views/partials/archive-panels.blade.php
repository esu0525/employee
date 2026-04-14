@if($canViewArchive)
<!-- Resign Tab -->
<div id="resignTab" class="tab-pane {{ $active_tab == 'resign' ? 'active' : '' }}">
    @include('partials.archive-table', ['employees' => $resign, 'type' => 'resign', 'icon' => 'user-minus', 'badge' => 'badge-danger', 'label' => 'Resigned'])
    @include('partials.archive-pagination', ['employees' => $resign, 'tab' => 'resign'])
</div>

<!-- Retired Tab -->
<div id="retiredTab" class="tab-pane {{ $active_tab == 'retired' ? 'active' : '' }}">
    @include('partials.archive-table', ['employees' => $retired, 'type' => 'retired', 'icon' => 'award', 'badge' => 'badge-warning', 'label' => 'Retired'])
    @include('partials.archive-pagination', ['employees' => $retired, 'tab' => 'retired'])
</div>

<!-- Transfer Tab -->
<div id="transferTab" class="tab-pane {{ $active_tab == 'transfer' ? 'active' : '' }}">
    @include('partials.archive-table', ['employees' => $transfer, 'type' => 'transfer', 'icon' => 'arrow-right-left', 'badge' => 'badge-info', 'label' => 'Transferred'])
    @include('partials.archive-pagination', ['employees' => $transfer, 'tab' => 'transfer'])
</div>

<!-- Others Tab -->
<div id="othersTab" class="tab-pane {{ $active_tab == 'others' ? 'active' : '' }}">
    @include('partials.archive-table', ['employees' => $others, 'type' => 'others', 'icon' => 'more-horizontal', 'badge' => 'badge-primary', 'label' => 'Others'])
    @include('partials.archive-pagination', ['employees' => $others, 'tab' => 'others'])
</div>
@endif

<!-- Reports Tab -->
<div id="reportsTab" class="tab-pane {{ $active_tab == 'reports' ? 'active' : '' }}">
    <div class="table-container shadow-sm">
        <div class="table-wrapper">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Report Title</th>
                        <th>Period Coverage</th>
                        <th>Details</th>
                        <th>Date Generated</th>
                        <th style="width: 140px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody id="reportsTableBody">
                    <!-- Loaded via JS -->
                    <tr class="loading-reports">
                        <td colspan="5" style="text-align: center; padding: 3rem;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem; color: var(--text-muted);">
                                <i data-lucide="loader" class="animate-spin" style="width: 2rem; height: 2rem;"></i>
                                <p style="font-weight: 600;">Loading reports...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
    // After partial swap, we need to ensure the correct tab is visible
    const slider = document.getElementById('tab-slider');
    const activeTabBtn = document.getElementById('btn-{{ $active_tab }}');
    if (slider && activeTabBtn) {
        slider.style.left = activeTabBtn.offsetLeft + 'px';
        slider.style.width = activeTabBtn.offsetWidth + 'px';
    }
</script>
