document.addEventListener('DOMContentLoaded', function() {
    attachPaginationLinks();
    
    // Check for success toast timeout specifically if it was injected on load
    const toast = document.getElementById('successToast');
    if (toast) {
        setTimeout(() => {
            toast.style.display = 'none';
        }, 5000);
    }
});

function filterLogs(page = 1) {
    const search = document.getElementById('logSearch').value;
    const year = document.getElementById('yearFilter').value;
    const month = document.getElementById('monthFilter').value;
    const container = document.getElementById('logsTableContainer');

    if (!container) return;
    
    // Show loading state
    container.style.opacity = '0.5';

    const params = new URLSearchParams({ search, year, month, page });
    
    // Update URL for sharability/history state without full reload
    const url = new URL(window.location.href);
    url.searchParams.set('search', search);
    url.searchParams.set('year', year);
    url.searchParams.set('month', month);
    url.searchParams.set('page', page);
    window.history.pushState({}, '', url);

    const filterUrl = container.getAttribute('data-filter-url');

    fetch(`${filterUrl}?${params}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.text())
    .then(html => {
        container.innerHTML = html;
        container.style.opacity = '1';
        if (typeof lucide !== 'undefined') lucide.createIcons();
        attachPaginationLinks();
    })
    .catch(err => {
        console.error(err);
        container.style.opacity = '1';
    });
}
window.filterLogs = filterLogs;

function resetFilters() {
    if(document.getElementById('logSearch')) document.getElementById('logSearch').value = '';
    if(document.getElementById('yearFilter')) document.getElementById('yearFilter').value = 'all';
    if(document.getElementById('monthFilter')) document.getElementById('monthFilter').value = 'all';
    filterLogs();
}
window.resetFilters = resetFilters;

function attachPaginationLinks() {
    const links = document.querySelectorAll('.pagination-ajax');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            filterLogs(page);
        });
    });
}
window.attachPaginationLinks = attachPaginationLinks;

function openCleanupModal() {
    const modal = document.getElementById('cleanupModal');
    if (modal) modal.style.display = 'flex';
}
window.openCleanupModal = openCleanupModal;

function closeCleanupModal() {
    const modal = document.getElementById('cleanupModal');
    if (modal) modal.style.display = 'none';
}
window.closeCleanupModal = closeCleanupModal;

function showConfirmStep() {
    const start = document.getElementById('cleanStart').value;
    const end = document.getElementById('cleanEnd').value;
    
    if(!start || !end) {
        alert('Please select both start and end dates.');
        return;
    }

    const dispStart = document.getElementById('dispStart');
    const dispEnd = document.getElementById('dispEnd');
    const confirmModal = document.getElementById('confirmDeleteModal');
    
    if (dispStart) dispStart.innerText = start;
    if (dispEnd) dispEnd.innerText = end;
    if (confirmModal) confirmModal.style.display = 'flex';
}
window.showConfirmStep = showConfirmStep;

function closeConfirmStep() {
    const modal = document.getElementById('confirmDeleteModal');
    if (modal) modal.style.display = 'none';
}
window.closeConfirmStep = closeConfirmStep;

function submitCleanupForm() {
    const form = document.getElementById('cleanupForm');
    if (form) form.submit();
}
window.submitCleanupForm = submitCleanupForm;
