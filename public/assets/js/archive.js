// Multi-Tab Archive Dashboard Logic
let currentTab = 'resign';

const config = {
    exportJsonUrl: '/employees/export-json',
    reportEmployeesUrl: '/employees/archive/report-employees',
    reportStoreUrl: '/archive/reports',
    reportsListUrl: '/archive/reports',
    reportedIdsUrl: '/archive/reported-employee-ids',
    csrfToken: '',
};

document.addEventListener('DOMContentLoaded', () => {
    // Save current URL for details page back button
    localStorage.setItem('archiveLastUrl', window.location.href);

    const panels = document.getElementById('panelsContainer');
    if (panels) {
        currentTab = panels.getAttribute('data-active-tab') || 'resign';
        config.exportJsonUrl = panels.getAttribute('data-export-url');
        config.reportEmployeesUrl = panels.getAttribute('data-report-url');
        config.reportStoreUrl = panels.getAttribute('data-report-store-url');
        config.reportsListUrl = panels.getAttribute('data-reports-list-url');
        config.reportedIdsUrl = panels.getAttribute('data-reported-ids-url');
        config.csrfToken = panels.getAttribute('data-csrf-token');
    }

    switchTab(currentTab, false);

    // Initial pagination setup for all tabs
    ['resign', 'retired', 'transfer', 'others'].forEach(tab => {
        updateTablePagination(tab);
    });

    // Check if filter was open
    if (localStorage.getItem('archiveFilterOpen') === 'true') {
        const menu = document.getElementById('filterMenu');
        if (menu) menu.classList.add('active');
    }

    // Success toast dismissal
    const toast = document.getElementById('successToast');
    if (toast) {
        setTimeout(() => {
            toast.style.display = 'none';
        }, 5000);
    }
    
    // Process initial highlights
    processArchiveHighlights();

    // Background polling: Refresh current tab every 10 seconds if not searching/filtering
    setInterval(() => {
        const searchInput = document.getElementById('archiveSearchInput');
        if (!searchInput || searchInput.value.trim() === '') {
            console.log('[Real-time] Syncing archive tab: ' + currentTab);
            const statusIndicator = document.getElementById('realtime-status-archive');
            if (statusIndicator) statusIndicator.querySelector('span').nextSibling.textContent = ' Syncing...';
            
            refreshArchiveTab(currentTab);
            
            setTimeout(() => {
                if (statusIndicator) statusIndicator.querySelector('span').nextSibling.textContent = ' Real-time Active';
            }, 2000);
        }
    }, 10000);

    // Lucide icons
    if (window.lucide) lucide.createIcons();
});

function processArchiveHighlights() {
    const seenIds = JSON.parse(localStorage.getItem('seen_archive_ids') || '[]');
    const rows = document.querySelectorAll('.hover-row[data-id]');
    const twoHoursAgo = Math.floor(Date.now() / 1000) - (2 * 60 * 60);

    rows.forEach(row => {
        const id = row.getAttribute('data-id');
        const ts = parseInt(row.getAttribute('data-timestamp') || '0');

        if (!seenIds.includes(id) && ts > twoHoursAgo) {
            row.classList.add('row-highlight-new');
            const badge = row.querySelector('.row-new-badge');
            if (badge) {
                badge.style.display = 'block';
                badge.style.opacity = '1';
            }

            setTimeout(() => {
                row.classList.remove('row-highlight-new');
                if (badge) {
                    badge.style.opacity = '0';
                    setTimeout(() => { badge.style.display = 'none'; }, 500);
                }

                const currentSeen = JSON.parse(localStorage.getItem('seen_archive_ids') || '[]');
                if (!currentSeen.includes(id)) {
                    currentSeen.push(id);
                    localStorage.setItem('seen_archive_ids', JSON.stringify(currentSeen));
                }
            }, 5000); // 5 seconds
        } else if (!seenIds.includes(id)) {
            // Auto-mark old as seen
            seenIds.push(id);
            localStorage.setItem('seen_archive_ids', JSON.stringify(seenIds));
        }
    });
}
window.processArchiveHighlights = processArchiveHighlights;

function refreshArchiveTab(tab) {
    if (tab === 'reports') return; // Skip reports
    
    const url = new URL(window.location.href);
    url.searchParams.set('tab', tab);
    
    fetch(url.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
    })
    .then(res => res.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newPane = doc.getElementById(tab + 'Tab');
        const currentPane = document.getElementById(tab + 'Tab');
        
        if (newPane && currentPane) {
            currentPane.innerHTML = newPane.innerHTML;
            
            // Re-apply search filter if active
            const searchInput = document.getElementById('archiveSearchInput');
            const term = searchInput ? searchInput.value.toLowerCase().trim() : '';
            if (term !== '') {
                currentPane.querySelectorAll('tbody tr:not(.empty-row)').forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const isMatch = text.includes(term);
                    row.classList.toggle('search-match', isMatch);
                    row.classList.toggle('search-hidden', !isMatch);
                });
            }
            
            // Critical fix: Re-apply pagination after content swap
            updateTablePagination(tab, term !== '');
            processArchiveHighlights();
        }
        if (window.lucide) lucide.createIcons();
    })
    .catch(err => console.error('Archive refresh failed:', err));
}

function switchTab(tab, updateUrl = true) {
    currentTab = tab;

    // Update Slider Position
    const slider = document.getElementById('tab-slider');
    const tabs = ['resign', 'retired', 'transfer', 'others', 'reports'];
    const idx = tabs.indexOf(tab);

    if (slider) {
        slider.style.left = `calc(${idx * 20}% + 0.375rem)`;
        slider.style.width = `calc(20% - 0.75rem)`;
    }

    // Update Tab Buttons
    document.querySelectorAll('.archive-tab').forEach(btn => {
        btn.classList.remove('active');
        if (btn.id === 'btn-' + tab) btn.classList.add('active');
    });

    // Update Panes & Attributes
    const panels = document.getElementById('panelsContainer');
    if (panels) panels.setAttribute('data-active-tab', tab);

    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('active');
        if (pane.id === tab + 'Tab') pane.classList.add('active');
    });

    if (tab === 'reports') {
        loadArchiveReports();
    }

    // Update hidden inputs
    const searchTabInput = document.getElementById('searchTabInput');
    const filterTabInput = document.getElementById('filterTabInput');
    if (searchTabInput) searchTabInput.value = tab;
    if (filterTabInput) filterTabInput.value = tab;

    if (updateUrl) {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url);
        // Also update the "back" destination URL
        localStorage.setItem('archiveLastUrl', window.location.href);
    }
}
window.switchTab = switchTab;

function toggleFilterMenu() {
    const menu = document.getElementById('filterMenu');
    if (!menu) return;
    const isActive = menu.classList.toggle('active');
    localStorage.setItem('archiveFilterOpen', isActive);
}
window.toggleFilterMenu = toggleFilterMenu;

function toggleSortMenu() {
    const menu = document.getElementById('sortMenu');
    if (menu) menu.classList.toggle('active');
}
window.toggleSortMenu = toggleSortMenu;

function submitWithFilter() {
    const form = document.getElementById('filterForm');
    if (form) form.submit();
}
window.submitWithFilter = submitWithFilter;

function submitWithSort(sort) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sort);
    window.location.href = url.toString();
}
window.submitWithSort = submitWithSort;

// Report Generator Logic
let allReportEmployees = [];
let reportSelectedIds = new Set();
let reportActiveCategory = 'all';

function openReportModal() {
    // Reset all state on reopen
    reportSelectedIds = new Set();
    reportActiveCategory = 'all';

    // Reset search input
    const searchInput = document.getElementById('reportSearchFilter');
    if (searchInput) searchInput.value = '';

    // Reset select-all checkbox
    const selectAll = document.getElementById('selectAllReport');
    if (selectAll) selectAll.checked = false;

    // Reset selected count badge
    const badge = document.getElementById('selectedReportCount');
    if (badge) badge.innerText = '0 Selected';

    // Reset category tab buttons
    const tabFilters = document.querySelectorAll('#reportTabFilters button');
    tabFilters.forEach(b => {
        b.className = 'btn btn-outline';
        b.style.border = '1px solid var(--border)';
    });
    // Highlight the "All" tab
    if (tabFilters.length > 0) {
        tabFilters[0].className = 'btn btn-primary';
        tabFilters[0].style.border = 'none';
    }

    const modal = document.getElementById('reportSelectionModal');
    if (modal) modal.classList.add('active');
    loadReportEmployees();
}
window.openReportModal = openReportModal;

function closeReportModal() {
    const modal = document.getElementById('reportSelectionModal');
    if (modal) modal.classList.remove('active');
}
window.closeReportModal = closeReportModal;

async function loadReportEmployees() {
    const container = document.getElementById('reportListContainer');
    const indicator = document.getElementById('reportLoadingIndicator');

    if (!container) return;
    if (indicator) indicator.style.display = 'flex';

    try {
        // Fetch employees and reported IDs in parallel
        const [empRes, reportedRes] = await Promise.all([
            fetch(config.reportEmployeesUrl),
            fetch(config.reportedIdsUrl)
        ]);
        
        allReportEmployees = await empRes.json();
        reportedEmployeeIds = await reportedRes.json();
        
        renderReportList();
    } catch (err) {
        console.error('Failed to load report employees:', err);
        container.innerHTML = '<div style="padding: 2rem; text-align: center; color: #ef4444; font-weight: 600;">Failed to load records.</div>';
    } finally {
        if (indicator) indicator.style.display = 'none';
    }
}

function renderReportList() {
    const container = document.getElementById('reportListContainer');
    if (!container) return;

    const query = document.getElementById('reportSearchFilter').value.toLowerCase().trim();
    const sortBy = document.getElementById('reportModalSort').value;

    // Filter out employees already in a report
    let filtered = allReportEmployees.filter(e => !reportedEmployeeIds.includes(String(e.id)));

    if (reportActiveCategory !== 'all') {
        filtered = filtered.filter(e => e.status === reportActiveCategory);
    }
    if (query) {
        filtered = filtered.filter(e => e.name.toLowerCase().includes(query));
    }

    // Sort the list
    filtered.sort((a, b) => {
        if (sortBy === 'sep_newest') {
            return new Date(b.effective_date || 0) - new Date(a.effective_date || 0);
        } else if (sortBy === 'archived_newest') {
            return new Date(b.updated_at || b.created_at || 0) - new Date(a.updated_at || a.created_at || 0);
        }
        return 0;
    });

    container.innerHTML = filtered.map(emp => {
        let detailsText = '';
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const d = emp.effective_date ? new Date(emp.effective_date) : null;
        const dateStr = d ? `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}` : 'No date';

        if (emp.status === 'resign') {
            detailsText = `<span style="display: flex; align-items: center; gap: 4px; color: var(--danger);"><i data-lucide="user-minus" style="width: 12px; height: 12px;"></i> RESIGNATION</span>`;
        } else if (emp.status === 'retired') {
            let ru = emp.retirement_under || emp.status_specify || '';
            let text = ru ? `RETIREMENT UNDER ${ru.replace(/retirement under/i, '').trim().toUpperCase()}` : 'RETIREMENT';
            if (ru.match(/R\.?A\.?/i)) text = text.replace(/R\.?A\.?/i, 'R.A. ');
            detailsText = `<span style="display: flex; align-items: center; gap: 4px; color: var(--warning);"><i data-lucide="award" style="width: 12px; height: 12px;"></i> ${text}</span>`;
        } else if (emp.status === 'transfer') {
            let loc = emp.transfer_to || emp.status_specify || '-';
            detailsText = `<span style="display: flex; align-items: center; gap: 4px; color: var(--info);"><i data-lucide="arrow-right-left" style="width: 12px; height: 12px;"></i> TO: ${loc.toUpperCase()}</span>`;
        } else {
            let sp = emp.status_specify || 'OTHERS';
            detailsText = `<span style="display: flex; align-items: center; gap: 4px; color: var(--primary);"><i data-lucide="more-horizontal" style="width: 12px; height: 12px;"></i> ${sp.toUpperCase()}</span>`;
        }

        return `
        <label class="report-item-label" style="display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1.25rem; background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; cursor: pointer; transition: 0.2s; margin-bottom: 0.5rem;">
            <input type="checkbox" class="report-employee-checkbox" value="${emp.id}" 
                   ${reportSelectedIds.has(String(emp.id)) ? 'checked' : ''} 
                   onchange="toggleOneReportCheckbox(this)"
                   style="width: 18px; height: 18px; accent-color: var(--primary);">
            <div style="flex: 1; display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <span style="font-weight: 700; color: var(--text-main); font-size: 0.9rem;">${emp.name}</span>
                    <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 700;">${dateStr}</span>
                </div>
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">${emp.position}</span>
                <div style="font-size: 0.7rem; font-weight: 800; margin-top: 4px;">${detailsText}</div>
            </div>
            <span class="badge ${getBadgeClass(emp.status)}" style="font-size: 0.65rem; padding: 0.25rem 0.65rem;">${emp.status.toUpperCase()}</span>
        </label>
        `;
    }).join('');

    // Re-initialize icons dynamically
    setTimeout(() => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 10);

    if (filtered.length === 0) {
        container.innerHTML = '<div style="padding: 3rem; text-align: center; color: var(--text-muted); font-weight: 600;">No available records found matching your criteria.</div>';
    }

    // Uncheck select all if some are missing
    const selectAll = document.getElementById('selectAllReport');
    if (selectAll) {
        selectAll.checked = filtered.length > 0 && filtered.every(e => reportSelectedIds.has(String(e.id)));
    }
}

function getBadgeClass(status) {
    if (status === 'resign') return 'badge-danger';
    if (status === 'retired') return 'badge-warning';
    if (status === 'transfer') return 'badge-info';
    return 'badge-primary';
}

function filterReportList() {
    renderReportList();
}
window.filterReportList = filterReportList;

function setReportTab(tab, btn) {
    reportActiveCategory = tab;
    // Highlight button
    document.querySelectorAll('#reportTabFilters button').forEach(b => {
        b.className = 'btn btn-outline';
        b.style.border = '1px solid var(--border)';
    });
    btn.className = 'btn btn-primary';
    btn.style.border = 'none';
    renderReportList();
}
window.setReportTab = setReportTab;

function toggleOneReportCheckbox(cb) {
    if (cb.checked) reportSelectedIds.add(cb.value);
    else reportSelectedIds.delete(cb.value);
    updateReportCount();
}
window.toggleOneReportCheckbox = toggleOneReportCheckbox;

function toggleAllReportCheckboxes() {
    const isChecked = document.getElementById('selectAllReport').checked;
    const checkboxes = document.querySelectorAll('.report-employee-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = isChecked;
        if (isChecked) reportSelectedIds.add(cb.value);
        else reportSelectedIds.delete(cb.value);
    });
    updateReportCount();
}
window.toggleAllReportCheckboxes = toggleAllReportCheckboxes;

function updateReportCount() {
    const badge = document.getElementById('selectedReportCount');
    if (badge) badge.innerText = reportSelectedIds.size + ' Selected';
}

function openReportConfirmModal() {
    if (reportSelectedIds.size === 0) {
        alert('Please select at least one employee for the report.');
        return;
    }

    // Reset and populate confirm modal form fields
    const titleInput = document.getElementById('reportTitleInput');
    const periodInput = document.getElementById('reportPeriodInput');
    const officeInput = document.getElementById('reportOfficeInput');
    const fileNameInput = document.getElementById('reportFileNameInput');
    const formatSelect = document.getElementById('reportFormat');

    const defaultTitle = 'CONSOLIDATED REPORT ON SEPARATION (Schools Division Office, Quezon City) (Non-Teaching Only)';
    const defaultOffice = 'CSC FO NIA';
    const defaultFileName = 'Custom_Separation_Report_' + new Date().getTime();

    if (titleInput && (!titleInput.value || titleInput.value.trim() === '')) {
        titleInput.value = defaultTitle;
    }
    if (periodInput) periodInput.value = ''; // Let it be calculated automatically unless specified
    if (officeInput && (!officeInput.value || officeInput.value.trim() === '')) {
        officeInput.value = defaultOffice;
    }
    if (fileNameInput && (!fileNameInput.value || fileNameInput.value.trim() === '')) {
        fileNameInput.value = defaultFileName;
    }
    const modal = document.getElementById('reportConfirmModal');
    if (modal) modal.classList.add('active');
}
window.openReportConfirmModal = openReportConfirmModal;

function closeReportConfirmModal() {
    const modal = document.getElementById('reportConfirmModal');
    if (modal) modal.classList.remove('active');
}
window.closeReportConfirmModal = closeReportConfirmModal;

async function generateReportFinal() {
    const format = document.getElementById('reportFormat').value;
    const title = document.getElementById('reportTitleInput').value.trim() || 'Separation Record List';
    const office = document.getElementById('reportOfficeInput').value.trim() || 'Regional Office No. IV';
    const filename = document.getElementById('reportFileNameInput').value.trim() || 'Archive_Report';

    const selectedEmployees = allReportEmployees.filter(e => reportSelectedIds.has(String(e.id)));
    const selectedIds = selectedEmployees.map(e => String(e.id));

    // Auto-calculate Period Coverage
    let period = document.getElementById('reportPeriodInput').value.trim();
    if (!period) {
        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const validDates = selectedEmployees
            .filter(e => e.effective_date)
            .map(e => new Date(e.effective_date))
            .filter(d => !isNaN(d.getTime()));

        if (validDates.length > 0) {
            validDates.sort((a, b) => a - b);
            const earliest = validDates[0];
            const latest = validDates[validDates.length - 1];
            const fmtDate = (d) => `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`;
            period = `${fmtDate(earliest)} to ${fmtDate(latest)}`;
        } else {
            period = `Year ${new Date().getFullYear()}`;
        }
    }

    // 1. Save to Database
    try {
        const res = await fetch(config.reportStoreUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': config.csrfToken
            },
            body: JSON.stringify({
                title: title,
                period_coverage: period,
                regional_office: office,
                file_name: filename,
                format: format,
                employee_ids: selectedIds
            })
        });
        
        if (!res.ok) {
            const errData = await res.json();
            throw new Error(errData.message || 'Failed to save report to database');
        }
        
        // Refresh reports list if we're on the reports tab
        if (currentTab === 'reports') loadArchiveReports();
    } catch (err) {
        console.error('Error saving report to DB:', err);
        alert('Warning: Generated report may not be saved to the database: ' + err.message);
    }

    // 2. Client-side Generation & Download
    if (format === 'pdf') {
        generateReportPDF(selectedEmployees, title, period, office, filename);
    } else {
        generateReportExcel(selectedEmployees, title, period, office, filename);
    }

    closeReportConfirmModal();
    closeReportModal();
}
window.generateReportFinal = generateReportFinal;

async function loadArchiveReports() {
    const tbody = document.getElementById('reportsTableBody');
    if (!tbody) return;

    try {
        const res = await fetch(config.reportsListUrl);
        const reports = await res.json();
        renderArchiveReports(reports);
    } catch (err) {
        console.error('Failed to load reports:', err);
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--danger); padding: 2rem;">Failed to load reports.</td></tr>';
    }
}
window.loadArchiveReports = loadArchiveReports;

function renderArchiveReports(reports) {
    const tbody = document.getElementById('reportsTableBody');
    if (!tbody) return;

    if (reports.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 5rem;">No reports generated yet.</td></tr>';
        return;
    }

    tbody.innerHTML = reports.map(r => {
        const date = new Date(r.created_at);
        const dateStr = `${date.toLocaleDateString()} ${date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;
        const formatIcon = r.format === 'pdf' ? 'file-text' : 'file-spreadsheet';
        const formatColor = r.format === 'pdf' ? '#ef4444' : '#10b981';

        return `
            <tr class="hover-row">
                <td>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 32px; height: 32px; background: ${formatColor}20; color: ${formatColor}; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="${formatIcon}" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-weight: 700; color: var(--text-main); font-size: 0.85rem;">${r.title}</span>
                            <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">${r.file_name}.${r.format}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-main);">${r.period_coverage || '-'}</div>
                </td>
                <td>
                    <div style="display: flex; flex-direction: column;">
                        <span style="font-size: 0.75rem; color: var(--text-main); font-weight: 700;">${r.employee_count} Employees</span>
                        <span style="font-size: 0.65rem; color: var(--text-muted); font-weight: 600;">By: ${r.generated_by || 'System'}</span>
                    </div>
                </td>
                <td>
                    <div style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted);">${dateStr}</div>
                </td>
                <td>
                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                        <button onclick="viewArchiveReport(${r.id})" class="btn btn-outline" style="width: 32px; height: 32px; padding: 0; border-radius: 8px;" title="View Report">
                            <i data-lucide="eye" style="width: 16px; color: var(--primary);"></i>
                        </button>
                        <button onclick="redownloadArchiveReport(${r.id})" class="btn btn-outline" style="width: 32px; height: 32px; padding: 0; border-radius: 8px;" title="Download File">
                            <i data-lucide="download" style="width: 16px; color: var(--primary);"></i>
                        </button>
                        <button onclick="deleteArchiveReport(${r.id})" class="btn btn-outline" style="width: 32px; height: 32px; padding: 0; border-radius: 8px;" title="Delete Record">
                            <i data-lucide="trash-2" style="width: 16px; color: var(--danger);"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

let currentViewerReport = null;

async function viewArchiveReport(reportId) {
    const modal = document.getElementById('reportViewModal');
    const iframe = document.getElementById('reportViewIframe');
    const excelDiv = document.getElementById('reportViewExcel');
    const loading = document.getElementById('reportViewLoading');
    const title = document.getElementById('reportViewTitle');
    const sub = document.getElementById('reportViewSub');

    try {
        const res = await fetch(`${config.reportsListUrl}`);
        const reports = await res.json();
        const r = reports.find(report => report.id === reportId);
        
        if (!r) throw new Error('Report not found');
        currentViewerReport = r;
        
        // NEW TAB FOR EXCEL in GSheets
        if (r.format !== 'pdf') {
            const reportUrl = `${config.reportDownloadRoot}/${r.id}/download`;
            const gEmbedUrl = `https://docs.google.com/viewer?url=${encodeURIComponent(reportUrl)}&embedded=true`;
            window.open(gEmbedUrl, '_blank');
            return;
        }

        if (!modal) return;
        
        // Reset state for PDF modal viewing
        modal.classList.add('active');
        loading.style.display = 'flex';
        iframe.style.display = 'none';
        excelDiv.style.display = 'none';
        iframe.src = 'about:blank';
        
        title.innerText = r.title;
        sub.innerText = `${r.file_name}.pdf • Generated on ${new Date(r.created_at).toLocaleDateString()}`;

        // Fetch the employees included in this report
        const empRes = await fetch(config.reportEmployeesUrl + '?tab=all');
        const allEmps = await empRes.json();
        const selectedEmployees = allEmps.filter(e => r.employee_ids.includes(String(e.id)));
        
        generateReportPDF(selectedEmployees, r.title, r.period_coverage, r.regional_office, r.file_name, true);
    } catch (err) {
        console.error('View failed:', err);
        alert('Failed to view report: ' + err.message);
        if (modal) closeReportViewModal();
    }
}
window.viewArchiveReport = viewArchiveReport;

window.switchToNativeExcelView = function() {
    const iframe = document.getElementById('reportViewIframe');
    const excelDiv = document.getElementById('reportViewExcel');
    const loading = document.getElementById('reportViewLoading');
    
    if (iframe) iframe.style.display = 'none';
    if (loading) loading.style.display = 'none';
    if (excelDiv) excelDiv.style.display = 'block';
}

function closeReportViewModal() {
    const modal = document.getElementById('reportViewModal');
    if (modal) {
        modal.classList.remove('active');
        // If it was fullscreen, exit
        if (document.fullscreenElement) {
            document.exitFullscreen();
        }
        // Remove fs class if exists
        modal.querySelector('.modal-content').classList.remove('fullscreen');
    }
    const iframe = document.getElementById('reportViewIframe');
    if (iframe) iframe.src = 'about:blank';
    currentViewerReport = null;
}
window.closeReportViewModal = closeReportViewModal;

function toggleReportFullScreen() {
    const modalContent = document.querySelector('#reportViewModal .modal-content');
    const btnText = document.querySelector('#reportViewModal .fs-text');
    const icon = document.querySelector('#reportViewModal .btn [data-lucide="maximize"], #reportViewModal .btn [data-lucide="minimize"]');
    
    if (!modalContent) return;

    if (!document.fullscreenElement) {
        modalContent.parentElement.requestFullscreen().catch(err => {
            console.error(`Error attempting to enable full-screen mode: ${err.message}`);
        });
        if (btnText) btnText.innerText = 'Exit Full Screen';
        // Swap icon via HTML if possible
    } else {
        document.exitFullscreen();
        if (btnText) btnText.innerText = 'Full Screen';
    }
}
window.toggleReportFullScreen = toggleReportFullScreen;

function redownloadCurrentViewReport() {
    if (!currentViewerReport) return;
    
    const excelDiv = document.getElementById('reportViewExcel');
    if (excelDiv && excelDiv.style.display === 'block') {
        // We are in native spreadsheet editor mode - read from DOM
        generateReportExcelFromDOM();
    } else {
        // We are in PDF or GSheet preview (original) - download original
        redownloadArchiveReport(currentViewerReport.id);
    }
}
window.redownloadCurrentViewReport = redownloadCurrentViewReport;

function generateReportExcelFromDOM() {
    const table = document.getElementById('excelPreviewTable');
    if (!table || typeof ExcelJS === 'undefined') return;

    const rows = Array.from(table.rows);
    const dataRows = rows.slice(3); // Skip A,B,C, Title, Header rows
    
    const workbook = new ExcelJS.Workbook();
    const sheet = workbook.addWorksheet('Edited Report');
    
    // Exact column headers as in generated reports
    const columns = ['#', 'AGENCY', 'NAME', 'POSITION TITLE', 'SG', 'LEVEL', 'STATUS', 'EFFECTIVITY', 'MODE'];
    
    // Add columns with some width
    sheet.columns = columns.map(c => ({ header: c, width: 20 }));
    
    dataRows.forEach(tr => {
        const cells = Array.from(tr.cells).slice(1); // skip row number
        const rowData = cells.map(td => td.innerText.trim());
        sheet.addRow(rowData);
    });

    workbook.xlsx.writeBuffer().then(buffer => {
        saveAs(new Blob([buffer]), `Edited_${currentViewerReport.file_name}.xlsx`);
    });
}

function renderExcelPreview(employees, title, period, office, isBackground = false) {
    const excelDiv = document.getElementById('reportViewExcel');
    const table = document.getElementById('excelPreviewTable');
    const loading = document.getElementById('reportViewLoading');
    
    if (!excelDiv || !table) return;

    // Header A-I like Excel
    const colLabels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
    let headerRow = '<tr><th class="gsheet-col-header"></th>';
    colLabels.forEach(l => headerRow += `<th class="gsheet-col-header">${l}</th>`);
    headerRow += '</tr>';

    let html = `
        <thead>
            ${headerRow}
            <tr>
                <th class="gsheet-row-header">1</th>
                <th colspan="9" style="text-align: left; padding: 1rem; border-bottom: 2px solid var(--primary); background: white;">
                    <div style="font-weight: 800; color: var(--text-main); font-size: 1.1rem;">${title}</div>
                    <div style="font-size: 0.85rem; color: #64748b; font-weight: 700; margin-top: 4px;">Period: ${period} | Office: ${office}</div>
                </th>
            </tr>
            <tr style="background: #f8fafc; text-align: center;">
                <th class="gsheet-row-header">2</th>
                <th style="font-weight: 800; color: #475569;">#</th>
                <th style="font-weight: 800; color: #475569;">AGENCY</th>
                <th style="font-weight: 800; color: #475569;">NAME</th>
                <th style="font-weight: 800; color: #475569;">POSITION TITLE</th>
                <th style="font-weight: 800; color: #475569;">SG</th>
                <th style="font-weight: 800; color: #475569;">LEVEL</th>
                <th style="font-weight: 800; color: #475569;">STATUS</th>
                <th style="font-weight: 800; color: #475569;">EFFECTIVITY</th>
                <th style="font-weight: 800; color: #475569;">MODE</th>
            </tr>
        </thead>
        <tbody>
    `;

    employees.forEach((e, i) => {
        let effDateStr = '';
        if (e.effective_date) {
            const d = new Date(e.effective_date);
            effDateStr = d.toLocaleDateString();
        }

        let mName = (e.name || '').toUpperCase();
        if (mName.includes(',')) {
            let parts = mName.split(',');
            mName = (parts[1].trim() + ' ' + parts[0].trim()).toUpperCase();
        }

        html += `
            <tr style="height: 36px;">
                <td class="gsheet-row-header">${i + 3}</td>
                <td style="text-align: center; color: #94a3b8; font-weight: 700; background: #fff;">${i + 1}</td>
                <td contenteditable="true" style="background: #fff;">${(e.agency || e.school || '-').toUpperCase()}</td>
                <td contenteditable="true" style="font-weight: 700; color: #2563eb; background: #fff;">${mName}</td>
                <td contenteditable="true" style="font-size: 9px; background: #fff;">${(e.position || '-').toUpperCase()}</td>
                <td contenteditable="true" style="text-align: center; background: #fff;">${e.salary_grade || '-'}</td>
                <td contenteditable="true" style="text-align: center; font-size: 9px; background: #fff;">${(e.level_of_position || '-').toUpperCase()}</td>
                <td contenteditable="true" style="font-size: 9px; font-weight: 700; background: #fff;">${(e.employment_status || '-').toUpperCase()}</td>
                <td contenteditable="true" style="font-size: 10px; text-align: center; background: #fff;">${effDateStr}</td>
                <td contenteditable="true" style="background: #fff;">${e.status.toUpperCase()}</td>
            </tr>
        `;
    });

    html += '</tbody>';
    table.innerHTML = html;
    
    if (!isBackground) {
        loading.style.display = 'none';
        excelDiv.style.display = 'block';
    }
}

async function redownloadArchiveReport(reportId) {
    try {
        const res = await fetch(`${config.reportsListUrl}`);
        const reports = await res.json();
        const r = reports.find(report => report.id === reportId);
        
        if (!r) throw new Error('Report not found');

        // Fetch the employees included in this report
        const empRes = await fetch(config.reportEmployeesUrl + '?tab=all');
        const allEmps = await empRes.json();
        
        // Filter those in report.employee_ids
        const selectedEmployees = allEmps.filter(e => r.employee_ids.includes(String(e.id)));
        
        if (r.format === 'pdf') {
            generateReportPDF(selectedEmployees, r.title, r.period_coverage, r.regional_office, r.file_name);
        } else {
            generateReportExcel(selectedEmployees, r.title, r.period_coverage, r.regional_office, r.file_name);
        }
    } catch (err) {
        console.error('Redownload failed:', err);
        alert('Failed to redownload report: ' + err.message);
    }
}
window.redownloadArchiveReport = redownloadArchiveReport;

async function deleteArchiveReport(id) {
    if (!confirm('Are you sure you want to delete this report record?')) return;

    try {
        const res = await fetch(`${config.reportsListUrl}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': config.csrfToken
            }
        });
        
        if (res.ok) {
            loadArchiveReports();
        } else {
            throw new Error('Deletion failed');
        }
    } catch (err) {
        console.error('Delete failed:', err);
        alert('Failed to delete report.');
    }
}
window.deleteArchiveReport = deleteArchiveReport;

// PDF Helpers
function safeText(doc, txt, x, y, options) {
    if (!isFinite(x) || !isFinite(y)) return;
    doc.text(String(txt || ''), x, y, options);
}

function generateReportPDF(employees, reportTitle, reportPeriod, reportOffice, reportFileName, viewOnly = false) {
    if (typeof jspdf === 'undefined') return;
    const { jsPDF } = jspdf;
    const doc = new jsPDF('l', 'mm', 'a4'); // landscape

    const pageWidth = doc.internal.pageSize.getWidth(); // ~297
    const leftMargin = 10;
    const rightMargin = 10;
    const tableWidth = pageWidth - leftMargin - rightMargin; // ~277

    // --- Title / Header Section (Professional Helvetica - Arial Substitute) ---
    doc.setTextColor(0, 0, 0);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9);

    const labelX = leftMargin + 5; 
    const valueX = 70; // Aligned with the left edge of the NAME column (leftMargin 10 + # 6 + Agency 54 = 70)
    safeText(doc, 'Title of Report:', labelX, 12);
    doc.setFont('helvetica', 'bold');
    safeText(doc, String(reportTitle).toUpperCase(), valueX, 12);

    // Row 2: Period Coverage
    doc.setFont('helvetica', 'normal');
    safeText(doc, 'Period Coverage:', labelX, 17);
    doc.setFont('helvetica', 'bold');
    safeText(doc, String(reportPeriod), valueX, 17);

    // Row 3: Regional Office
    doc.setFont('helvetica', 'normal');
    safeText(doc, 'Regional Office:', labelX, 22);
    doc.setFont('helvetica', 'bold');
    safeText(doc, String(reportOffice).toUpperCase(), valueX, 22);


    // --- Build table body ---
    const tableBody = employees.map((e, i) => {
        let effDateStr = '';
        if (e.effective_date) {
            const d = new Date(e.effective_date);
            const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            effDateStr = `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`;
        }

        let mName = (e.name || '').toUpperCase();
        if (mName.includes(',')) {
            let parts = mName.split(',');
            mName = (parts[1].trim() + ' ' + parts[0].trim()).toUpperCase();
        }

        let mPosition = (e.position || '').toUpperCase();
        let mAgency = (e.agency || e.school || '').toUpperCase();
        let mMode = e.status;

        if (mMode === 'resign') mMode = 'Resignation';
        else if (mMode === 'retired') {
            let ru = (e.retirement_under || e.status_specify || '').trim();
            ru = ru.replace(/retirement under/i, '').trim();
            mMode = ru ? `Retirement Under ${ru}` : 'Retirement';
        }
        else if (mMode === 'transfer') {
            let loc = (e.transfer_to || e.status_specify || '').trim();
            loc = loc.replace(/transferred to/i, '').trim();
            mMode = loc ? `Transferred to ${loc}` : 'Transferred';
        }
        else {
            let sp = (e.status_specify || 'Others').trim();
            mMode = sp;
        }

        // Title Case
        mMode = String(mMode).replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase());
        mMode = mMode.replace(/\bR\.?a\.?\b/gi, 'R.A. ');

        return [
            String(i + 1),
            mAgency,
            mName,
            mPosition,
            e.salary_grade || '',
            e.level_of_position || '',
            (e.employment_status || '').toUpperCase(),
            effDateStr,
            mMode
        ];
    });

    // Closing row with x markers
    tableBody.push(['', 'x', 'x', 'x', 'x', 'x', 'x', 'x', 'x']);

    // --- Column widths to fill the page (total EXACTLY 277mm for A4 Landscape) ---
    // #=6, Agency=54, Name=35, Position=35, SalGrade=16, LevelPos=18, StatusAppt=30, EffDate=35, Mode=48
    const colWidths = [6, 54, 35, 35, 16, 18, 30, 35, 48];

    doc.autoTable({
        head: [
            [
                { content: '', styles: { lineWidth: 0, fillColor: false } },
                { content: 'SCHOOLS DIVISION OFFICE, QUEZON CITY', colSpan: 8, styles: { halign: 'left', fontStyle: 'bold', fillColor: [255, 255, 255], textColor: [0, 0, 0], lineWidth: 0.1, lineColor: [0, 0, 0] } }
            ],
            [
                { content: '', styles: { lineWidth: 0, fillColor: false } },
                'AGENCY', 'NAME', 'POSITION TITLE', 'SALARY\nGRADE', 'LEVEL OF\nPOSITION', 'STATUS OF\nAPPOINTMENT', 'EFFECTIVITY DATE\nOF SEPARATION', 'MODE OF SEPARATION'
            ]
        ],
        body: tableBody,
        startY: 30,
        theme: 'grid',
        styles: {
            fontSize: 9,
            font: 'helvetica',
            textColor: [0, 0, 0],
            lineColor: [0, 0, 0],
            lineWidth: 0.1,
            cellPadding: { top: 1.5, right: 1, bottom: 1.5, left: 1 }
        },
        headStyles: {
            fillColor: [255, 255, 255],
            textColor: [0, 0, 0],
            fontStyle: 'bold',
            halign: 'center',
            valign: 'middle',
            fontSize: 9
        },
        bodyStyles: { halign: 'center', valign: 'middle' },


        columnStyles: {
            0: { cellWidth: colWidths[0], halign: 'center' },
            1: { cellWidth: colWidths[1], halign: 'center' },
            2: { cellWidth: colWidths[2], halign: 'center' },
            3: { cellWidth: colWidths[3], halign: 'center' },
            4: { cellWidth: colWidths[4], halign: 'center' },
            5: { cellWidth: colWidths[5], halign: 'center' },
            6: { cellWidth: colWidths[6], halign: 'center' },
            7: { cellWidth: colWidths[7], halign: 'center' },
            8: { cellWidth: colWidths[8], halign: 'center' }
        },
        margin: { left: leftMargin, right: rightMargin },
        didParseCell: function (hookData) {
            // Remove border for the numbering column (column 0) to match image
            if (hookData.column.index === 0) {
                hookData.cell.styles.lineWidth = 0;
            }
        }
    });

    // --- Signatory Section ---
    // Mathematically align based on defined column widths:
    // Left Margin = 10. Col 0 (6mm) -> Col 1 (Agency) starts at 16mm, width 54mm. Center = 16 + 27 = 43mm.
    // Col 2 (35mm) -> Col 3 (Position) starts at 105mm. 
    // Col 3, 4, 5 combined widths = 35 + 16 + 18 = 69mm. Center = 105 + 34.5 = 139.5mm.
    const prepX = 43;
    const certX = 139.5;


    let finalY = 0;
    if (doc.lastAutoTable && isFinite(doc.lastAutoTable.finalY)) {
        finalY = doc.lastAutoTable.finalY + 20;
    } else {
        finalY = 150;
    }

    if (finalY > 190) {
        doc.addPage();
        finalY = 30;
    }

    doc.setTextColor(0, 0, 0);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9);
    safeText(doc, 'Prepared by:', prepX, finalY, { align: 'center' });
    safeText(doc, 'Certified Correct:', certX, finalY, { align: 'center' });

    finalY += 15;
    doc.setFont('helvetica', 'bold');
    doc.setDrawColor(0, 0, 0);

    // Use dynamic signatory names if available, else standard
    const prepName = document.getElementById('reportPreparedBy') ? document.getElementById('reportPreparedBy').value.toUpperCase() : 'MICHELLE A. MAL-IN';
    const certName = 'CARLEEN S. SEDILLA, CESO V';

    safeText(doc, prepName, prepX, finalY, { align: 'center' });
    const prepW = doc.getTextWidth(prepName);
    doc.setLineWidth(0.5);
    if (isFinite(prepX) && isFinite(finalY)) {
        doc.line(prepX - prepW / 2, finalY + 1, prepX + prepW / 2, finalY + 1);
    }

    safeText(doc, certName, certX, finalY, { align: 'center' });
    const certW = doc.getTextWidth(certName);
    if (isFinite(certX) && isFinite(finalY)) {
        doc.line(certX - certW / 2, finalY + 1, certX + certW / 2, finalY + 1);
    }

    finalY += 5;
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9);

    const prepTitle = document.getElementById('reportPreparedTitle') ? document.getElementById('reportPreparedTitle').value : 'Administrative Officer IV';
    const certTitle = 'Schools Division Superintendent';

    safeText(doc, prepTitle, prepX, finalY, { align: 'center' });
    safeText(doc, certTitle, certX, finalY, { align: 'center' });

    finalY += 15;
    let tDate = new Date();
    const mm = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    let tdStr = `${mm[tDate.getMonth()]} ${tDate.getDate()}, ${tDate.getFullYear()}`;

    safeText(doc, `Date: ${tdStr}`, prepX, finalY, { align: 'center' });
    safeText(doc, `Date: ${tdStr}`, certX, finalY, { align: 'center' });

    if (viewOnly) {
        const iframe = document.getElementById('reportViewIframe');
        const loading = document.getElementById('reportViewLoading');
        if (iframe) {
            iframe.src = doc.output('bloburl');
            iframe.style.display = 'block';
            if (loading) loading.style.display = 'none';
        }
    } else {
        doc.save(`${reportFileName}.pdf`);
    }
}

async function generateReportExcel(employees, reportTitle, reportPeriod, reportOffice, reportFileName) {
    if (typeof ExcelJS === 'undefined') return;
    const workbook = new ExcelJS.Workbook();
    const sheet = workbook.addWorksheet('Separation Records');

    // Use Arial Narrow, size 10 throughout
    const fontBase = { name: 'Arial Narrow', size: 10 };
    const fontBold = { name: 'Arial Narrow', size: 10, bold: true };
    const fontBoldUnderline = { name: 'Arial Narrow', size: 10, bold: true, underline: true };
    const thinBorder = {
        top: { style: 'thin' }, left: { style: 'thin' },
        bottom: { style: 'thin' }, right: { style: 'thin' }
    };

    // --- Row 1: Title of Report ---
    const row1 = sheet.addRow([]);
    // Column A is blank (#), Label in B, Value in C
    row1.getCell(2).value = 'Title of Report:';
    row1.getCell(2).font = fontBase;
    row1.getCell(3).value = String(reportTitle).toUpperCase();
    row1.getCell(3).font = fontBold;
    // Merge the value across C-I so it stays within the table
    sheet.mergeCells(1, 3, 1, 9);

    // --- Row 2: Period Coverage ---
    const row2 = sheet.addRow([]);
    row2.getCell(2).value = 'Period Coverage:';
    row2.getCell(2).font = fontBase;
    row2.getCell(3).value = String(reportPeriod);
    row2.getCell(3).font = fontBold;
    sheet.mergeCells(2, 3, 2, 9);

    // --- Row 3: Regional Office ---
    const row3 = sheet.addRow([]);
    row3.getCell(2).value = 'Regional Office:';
    row3.getCell(2).font = fontBase;
    row3.getCell(3).value = String(reportOffice).toUpperCase();
    row3.getCell(3).font = fontBold;
    sheet.mergeCells(3, 3, 3, 9);

    // --- Row 4: Blank ---
    sheet.addRow([]);

    // --- Row 5: SCHOOLS DIVISION OFFICE, QUEZON CITY ---
    const row5 = sheet.addRow([]);
    row5.getCell(2).value = 'SCHOOLS DIVISION OFFICE, QUEZON CITY';
    row5.getCell(2).font = fontBold;
    sheet.mergeCells(5, 2, 5, 9);
    // Apply border only within the table columns (B-I)
    for (let col = 2; col <= 9; col++) {
        row5.getCell(col).border = thinBorder;
    }

    // --- Row 6: Column Headers (10 columns: A=#, B=Agency, ..., J=Mode of Separation) ---
    const headers = sheet.addRow([
        '', 'AGENCY', 'NAME', 'POSITION TITLE', 'SALARY GRADE',
        'LEVEL OF POSITION', 'STATUS OF APPOINTMENT',
        'EFFECTIVITY DATE OF SEPARATION', 'MODE OF SEPARATION'
    ]);
    headers.height = 30;
    headers.eachCell((c, colNum) => {
        c.font = fontBold;
        c.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
        if (colNum >= 2) {
            c.border = thinBorder;
        }
    });

    // --- Data Rows ---
    employees.forEach((e, i) => {
        let effDateStr = '';
        if (e.effective_date) {
            const d = new Date(e.effective_date);
            const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            effDateStr = `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`;
        }

        let mName = (e.name || '').toUpperCase();
        if (mName.includes(',')) {
            let parts = mName.split(',');
            mName = (parts[1].trim() + ' ' + parts[0].trim()).toUpperCase();
        }

        let mMode = e.status;
        if (mMode === 'resign') mMode = 'Resignation';
        else if (mMode === 'retired') {
            let ru = (e.retirement_under || e.status_specify || '').trim();
            ru = ru.replace(/retirement under/i, '').trim();
            mMode = ru ? `Retirement Under ${ru}` : 'Retirement';
        }
        else if (mMode === 'transfer') {
            let loc = (e.transfer_to || e.status_specify || '').trim();
            loc = loc.replace(/transferred to/i, '').trim();
            mMode = loc ? `Transferred to ${loc}` : 'Transferred';
        }
        else {
            let sp = (e.status_specify || 'Others').trim();
            mMode = sp;
        }

        // Title Case
        mMode = String(mMode).replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase());
        mMode = mMode.replace(/\bR\.?a\.?\b/gi, 'R.A. ');

        const row = sheet.addRow([
            i + 1,
            (e.agency || e.school || '').toUpperCase(),
            mName,
            (e.position || '').toUpperCase(),
            e.salary_grade || '',
            (e.level_of_position || '').toUpperCase(),
            (e.employment_status || '').toUpperCase(),
            effDateStr,
            mMode
        ]);
        row.eachCell((c, colNum) => {
            c.font = fontBase;
            c.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
            if (colNum >= 2) {
                c.border = thinBorder;
            }
        });
    });

    // --- Closing row with x markers ---
    const closingRow = sheet.addRow(['', 'x', 'x', 'x', 'x', 'x', 'x', 'x', 'x']);
    closingRow.eachCell((c, colNum) => {
        c.font = fontBase;
        c.alignment = { horizontal: 'center', vertical: 'middle' };
        if (colNum >= 2) {
            c.border = thinBorder;
        }
    });

    // --- Blank rows before signatories ---
    sheet.addRow([]);
    sheet.addRow([]);
    sheet.addRow([]);

    // --- Signatory Row: Labels (centered via merged cells) ---
    const sigLabelRow = sheet.addRow([]);
    const slrNum = sigLabelRow.number;
    sigLabelRow.getCell(2).value = 'Prepared by:';
    sigLabelRow.getCell(2).font = fontBase;
    sigLabelRow.getCell(2).alignment = { horizontal: 'center' };
    // No merge for Prepared By: stays in Column B (2)
    sigLabelRow.getCell(4).value = 'Certified Correct:';
    sigLabelRow.getCell(4).font = fontBase;
    sigLabelRow.getCell(4).alignment = { horizontal: 'center' };
    sheet.mergeCells(slrNum, 4, slrNum, 6); // Merge D-F

    // Blank row
    sheet.addRow([]);

    // --- Signatory Row: Names (bold, underline, centered) ---
    const sigNameRow = sheet.addRow([]);
    const snrNum = sigNameRow.number;
    sigNameRow.getCell(2).value = 'MICHELLE A. MAL-IN';
    sigNameRow.getCell(2).font = fontBoldUnderline;
    sigNameRow.getCell(2).alignment = { horizontal: 'center' };
    sigNameRow.getCell(4).value = 'CARLEEN S. SEDILLA, CESO V';
    sigNameRow.getCell(4).font = fontBoldUnderline;
    sigNameRow.getCell(4).alignment = { horizontal: 'center' };
    sheet.mergeCells(snrNum, 4, snrNum, 6); // Merge D-F

    // --- Signatory Row: Titles (centered) ---
    const sigTitleRow = sheet.addRow([]);
    const strNum = sigTitleRow.number;
    sigTitleRow.getCell(2).value = 'Administrative Officer IV';
    sigTitleRow.getCell(2).font = fontBase;
    sigTitleRow.getCell(2).alignment = { horizontal: 'center' };
    sigTitleRow.getCell(4).value = 'Schools Division Superintendent';
    sigTitleRow.getCell(4).font = fontBase;
    sigTitleRow.getCell(4).alignment = { horizontal: 'center' };
    sheet.mergeCells(strNum, 4, strNum, 6); // Merge D-F

    // Blank row
    sheet.addRow([]);

    // --- Date Row (centered) ---
    let tDate = new Date();
    const mm = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    let tdStr = `${mm[tDate.getMonth()]} ${tDate.getDate()}, ${tDate.getFullYear()}`;

    const dateRow = sheet.addRow([]);
    const drNum = dateRow.number;
    dateRow.getCell(2).value = `Date: ${tdStr}`;
    dateRow.getCell(2).font = fontBase;
    dateRow.getCell(2).alignment = { horizontal: 'center' };
    dateRow.getCell(4).value = `Date: ${tdStr}`;
    dateRow.getCell(4).font = fontBase;
    dateRow.getCell(4).alignment = { horizontal: 'center' };
    sheet.mergeCells(drNum, 4, drNum, 6); // Merge D-F

    // --- Apply Default Font (Arial Narrow 10) to all columns so no cell defaults to size 11 ---
    for (let i = 1; i <= 9; i++) {
        sheet.getColumn(i).font = fontBase;
    }

    // --- Column Widths (Converted from exact pixels provided by user: Width = (Pixels - 5) / 7) ---
    sheet.getColumn(1).width = 5.14;    // A: 41px
    sheet.getColumn(2).width = 28.85;   // B: 207px
    sheet.getColumn(3).width = 26.42;   // C: 190px
    sheet.getColumn(4).width = 15.14;   // D: 111px
    sheet.getColumn(5).width = 9.71;    // E: 73px
    sheet.getColumn(6).width = 11.14;   // F: 83px
    sheet.getColumn(7).width = 18.00;   // G: 131px
    sheet.getColumn(8).width = 17.85;   // H: 130px
    sheet.getColumn(9).width = 33.00;   // I: 236px

    // --- Page Setup: Landscape A4 ---
    sheet.pageSetup = {
        paperSize: 9,           // A4
        orientation: 'landscape',
        fitToPage: true,
        fitToWidth: 1,
        fitToHeight: 0
    };

    const buffer = await workbook.xlsx.writeBuffer();
    saveAs(new Blob([buffer]), `${reportFileName}.xlsx`);
}

// Global UI handling for Archive
window.onclick = function (event) {
    const reportModal = document.getElementById('reportSelectionModal');
    const confirmModal = document.getElementById('reportConfirmModal');
    const exportModal = document.getElementById('archiveExportModal');
    const filterMenu = document.getElementById('filterMenu');
    const sortMenu = document.getElementById('sortMenu');

    if (event.target == reportModal) closeReportModal();
    if (event.target == confirmModal) closeReportConfirmModal();
    if (event.target == exportModal) closeExportModal();

    if (filterMenu && !filterMenu.contains(event.target) && !event.target.closest('.filter-toggle-btn')) {
        filterMenu.classList.remove('active');
        localStorage.setItem('archiveFilterOpen', 'false');
    }
    if (sortMenu && !sortMenu.contains(event.target) && !event.target.closest('.sort-toggle-btn')) {
        sortMenu.classList.remove('active');
    }
}

// Export Archive Simple Modal
function openExportModal() {
    const modal = document.getElementById('archiveExportModal');
    if (modal) modal.classList.add('active');
}
window.openExportModal = openExportModal;

function closeExportModal() {
    const modal = document.getElementById('archiveExportModal');
    if (modal) modal.classList.remove('active');
}
window.closeExportModal = closeExportModal;

async function startExport(format) {
    const year = document.getElementById('exportYear').value;
    const month = document.getElementById('exportMonth').value;
    const tab = document.getElementById('exportTab').value;

    try {
        const response = await fetch(`${config.exportJsonUrl}?year=${year}&month=${month}&tab=${tab}`);
        const data = await response.json();

        if (format === 'excel') exportExcelSimple(data, year, month, tab);
        else if (format === 'pdf') exportPDFSimple(data, year, month, tab);
        else exportDocsSimple(data, year, month, tab);

        closeExportModal();
    } catch (err) {
        console.error('Export failed:', err);
        alert('Export failed!');
    }
}
window.startExport = startExport;

// Helper simple exports
async function exportExcelSimple(data, year, month, tab) {
    if (typeof ExcelJS === 'undefined') return;
    const workbook = new ExcelJS.Workbook();
    const sheet = workbook.addWorksheet('Archive');
    const hideSoNo = (['resign', 'retired', 'others'].includes(tab));

    let cols = [{ header: 'Full Name', key: 'name', width: 40 }, { header: 'Date', key: 'date', width: 25 }];
    if (!hideSoNo) cols.push({ header: 'S.O No', key: 'so_no', width: 20 });
    cols.push({ header: 'Remarks', key: 'remarks', width: 60 });
    sheet.columns = cols;

    data.forEach(e => {
        let remarks = e.status_specify || e.transfer_to || e.retirement_under || '-';
        if (e.status === 'transfer' && remarks !== '-') {
            remarks = remarks.replace(/^(transferred to|transfer to)\s+/i, '').trim();
            remarks = 'Transferred to ' + remarks;
        }
        if (e.status === 'resign') remarks = 'resignation';
        if (e.status === 'retired') remarks = 'Retirement Under R.A 8921';
        let rowData = [e.name, e.effective_date];
        if (!hideSoNo) rowData.push(e.so_no || '-');
        rowData.push(remarks);
        sheet.addRow(rowData);
    });

    const buffer = await workbook.xlsx.writeBuffer();
    saveAs(new Blob([buffer]), `Archive_Export_${tab}.xlsx`);
}

function exportPDFSimple(data, year, month, tab) {
    if (typeof jspdf === 'undefined') return;
    const { jsPDF } = jspdf;
    const doc = new jsPDF('l', 'mm', 'a4');
    doc.text('Employee Archive Records', 148, 20, { align: 'center' });
    const hideSoNo = (['resign', 'retired', 'others'].includes(tab));
    let headers = ['FULL NAME', 'DATE'];
    if (!hideSoNo) headers.push('S.O NO');
    headers.push('REMARKS');
    doc.autoTable({
        head: [headers],
        body: data.map(e => {
            let remarks = e.status_specify || e.transfer_to || e.retirement_under || '-';
            if (e.status === 'transfer' && remarks !== '-') {
                remarks = remarks.replace(/^(transferred to|transfer to)\s+/i, '').trim();
                remarks = 'Transferred to ' + remarks;
            }
            let row = [e.name, e.effective_date];
            if (!hideSoNo) row.push(e.so_no || '-');
            row.push(remarks);
            return row;
        }),
        startY: 35
    });
    doc.save(`Archive_Export_${tab}.pdf`);
}

// State for client-side pagination
const archivePages = {
    resign: 1,
    retired: 1,
    transfer: 1,
    others: 1
};
const perPage = 10;

window.liveSearch = function (query) {
    const term = query.toLowerCase().trim();
    const container = document.getElementById('panelsContainer');
    if (!container) return;

    // Filter ALL tabs instantly
    ['resign', 'retired', 'transfer', 'others'].forEach(tab => {
        const pane = document.getElementById(tab + 'Tab');
        if (!pane) return;

        const rows = pane.querySelectorAll('tbody tr:not(.empty-row)');
        let matchCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const isMatch = text.includes(term);
            row.classList.toggle('search-match', isMatch);
            row.classList.toggle('search-hidden', !isMatch);
            if (isMatch) matchCount++;
        });

        // Reset page to 1 for this tab when searching
        archivePages[tab] = 1;
        updateTablePagination(tab, term !== '');
    });

    // AUTO-SWITCH TAB: If searching and current tab is empty but others have matches
    if (term.length >= 2) {
        const activeTab = container.getAttribute('data-active-tab') || currentTab;
        const currentPane = document.getElementById(activeTab + 'Tab');
        const currentMatches = currentPane ? currentPane.querySelectorAll('tbody tr.search-match').length : 0;

        if (currentMatches === 0) {
            for (const tab of ['resign', 'retired', 'transfer', 'others']) {
                const pane = document.getElementById(tab + 'Tab');
                const matches = pane ? pane.querySelectorAll('tbody tr.search-match').length : 0;
                if (matches > 0) {
                    window.switchTab(tab, false);
                    break;
                }
            }
        }
    }
};

function updateTablePagination(tab, isSearching = false) {
    const pane = document.getElementById(tab + 'Tab');
    if (!pane) return;

    const rows = Array.from(pane.querySelectorAll('tbody tr:not(.empty-row)'));
    const matchedRows = isSearching ? rows.filter(r => r.classList.contains('search-match')) : rows;
    const total = matchedRows.length;
    const totalPages = Math.max(1, Math.ceil(total / perPage));

    if (archivePages[tab] > totalPages) archivePages[tab] = totalPages;
    if (archivePages[tab] < 1) archivePages[tab] = 1;

    const start = (archivePages[tab] - 1) * perPage;
    const end = start + perPage;

    // Update Row Visibility
    rows.forEach(r => r.style.display = 'none');
    matchedRows.slice(start, end).forEach(r => r.style.display = 'table-row');

    // Update Empty State
    const emptyState = pane.querySelector('.empty-row');
    if (emptyState) emptyState.style.display = (total === 0) ? 'table-row' : 'none';

    // Update Pagination UI
    const footer = document.getElementById('pagination-' + tab);
    if (footer) {
        footer.querySelector('.first-item').innerText = total > 0 ? start + 1 : 0;
        footer.querySelector('.last-item').innerText = Math.min(end, total);
        footer.querySelector('.total-items').innerText = total;
        footer.querySelector('.last-page').innerText = totalPages;
        const input = footer.querySelector('.page-input');
        if (input) {
            input.value = archivePages[tab];
            input.max = totalPages;
        }

        // Disable nav buttons if necessary
        const prev = footer.querySelector('.btn-prev');
        const next = footer.querySelector('.btn-next');
        if (prev) {
            prev.disabled = (archivePages[tab] <= 1);
            prev.style.opacity = prev.disabled ? '0.4' : '1';
            prev.style.cursor = prev.disabled ? 'not-allowed' : 'pointer';
        }
        if (next) {
            next.disabled = (archivePages[tab] >= totalPages);
            next.style.opacity = next.disabled ? '0.4' : '1';
            next.style.cursor = next.disabled ? 'not-allowed' : 'pointer';
        }
    }
}

window.changePage = function (tab, delta) {
    archivePages[tab] += delta;
    const searchInput = document.getElementById('archiveSearchInput');
    updateTablePagination(tab, searchInput && searchInput.value.trim() !== '');
};

window.goToPage = function (tab, page) {
    archivePages[tab] = parseInt(page) || 1;
    const searchInput = document.getElementById('archiveSearchInput');
    updateTablePagination(tab, searchInput && searchInput.value.trim() !== '');
};

window.fetchData = function (page, query) {
    // This now only reloads content from server if needed (e.g. after update)
    location.reload();
};
