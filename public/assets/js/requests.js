/**
 * Request Center Module JavaScript
 * Handles real-time polling, tab switching, search filtering, and approval workflows.
 */

// State for client-side pagination in Requests
const requestPages = {
    pending: 1,
    approved: 1
};
const perPageRequests = 10;
let searchTimer = null;
let currentTab = 'pending';

document.addEventListener('DOMContentLoaded', function () {
    // Initial pagination setup
    ['pending', 'approved'].forEach(tab => {
        updateRequestsPagination(tab);
    });

    // Tab setup from URL
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'pending';
    switchTab(activeTab); 
    
    // Initial badge update to correct Blade's 'total' counts
    window.updateBadgeCounts();

    // Real-time Polling: Check for new updates every 8 seconds for snappier feel
    setInterval(() => {
        const searchInput = document.getElementById('liveSearchInput');
        if (!searchInput || searchInput.value.trim() === '') {
            console.log('[Real-time] Polling for new requests...');
            const statusIndicator = document.getElementById('realtime-status');
            const originalText = statusIndicator ? statusIndicator.innerHTML : '';
            if (statusIndicator) statusIndicator.querySelector('span').nextSibling.textContent = ' Syncing...';
            
            window.fetchRequestsData(requestPages[currentTab] || 1);
            
            setTimeout(() => {
                if (statusIndicator) statusIndicator.querySelector('span').nextSibling.textContent = ' Real-time Active';
            }, 2000);
        }
        window.updateBadgeCounts();
    }, 8000);
});

window.liveSearch = function(query) {
    const term = query.toLowerCase().trim();
    
    // Filter BOTH tables instantly
    ['pending', 'approved'].forEach(tab => {
        const pane = document.getElementById('tab-' + tab);
        if (!pane) return;
        
        const rows = pane.querySelectorAll('tbody tr:not(.empty-row)');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const isMatch = text.includes(term);
            row.classList.toggle('search-match', isMatch);
        });

        // Reset page to 1 for this tab when searching
        requestPages[tab] = 1;
        updateRequestsPagination(tab, term !== '');
    });
};

function updateRequestsPagination(tab, isSearching = false) {
    const pane = document.getElementById('tab-' + tab);
    if (!pane) return;

    const rows = Array.from(pane.querySelectorAll('tbody tr:not(.empty-row)'));
    const matchedRows = isSearching ? rows.filter(r => r.classList.contains('search-match')) : rows;
    const total = matchedRows.length;
    const totalPages = Math.max(1, Math.ceil(total / perPageRequests));

    if (requestPages[tab] > totalPages) requestPages[tab] = totalPages;
    if (requestPages[tab] < 1) requestPages[tab] = 1;

    const start = (requestPages[tab] - 1) * perPageRequests;
    const end = start + perPageRequests;

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
            input.value = requestPages[tab];
            input.max = totalPages;
        }
        
        const prev = footer.querySelector('.btn-prev');
        const next = footer.querySelector('.btn-next');
        if (prev) {
            prev.disabled = (requestPages[tab] <= 1);
            prev.style.opacity = prev.disabled ? '0.4' : '1';
        }
        if (next) {
            next.disabled = (requestPages[tab] >= totalPages);
            next.style.opacity = next.disabled ? '0.4' : '1';
        }
    }
}

window.changePageRequests = function(tab, delta) {
    requestPages[tab] += delta;
    const searchInput = document.getElementById('liveSearchInput');
    updateRequestsPagination(tab, searchInput && searchInput.value.trim() !== '');
};

window.goToPageRequests = function(tab, page) {
    requestPages[tab] = parseInt(page) || 1;
    const searchInput = document.getElementById('liveSearchInput');
    updateRequestsPagination(tab, searchInput && searchInput.value.trim() !== '');
};

window.switchTab = function(tabId) {
    const panes = document.querySelectorAll('.tab-pane');
    const btns = document.querySelectorAll('.tab-btn');
    
    panes.forEach(p => p.classList.remove('active'));
    btns.forEach(b => b.classList.remove('active'));
    
    const pane = document.getElementById('tab-' + tabId);
    const btn = document.getElementById('btn-' + tabId);
    if (pane) pane.classList.add('active');
    if (btn) btn.classList.add('active');
    
    // Update Slider
    const slider = document.getElementById('tab-slider');
    if (slider) {
        if (tabId === 'pending') {
            slider.style.left = '0.5rem';
        } else {
            slider.style.left = 'calc(50% - 0.05rem + 0.05rem)'; // Adjusted to match original feel
        }
    }

    const hiddenTabInput = document.querySelector('input[name="tab"]');
    if (hiddenTabInput) hiddenTabInput.value = tabId;
    
    currentTab = tabId;
    window.processHighlights(tabId);
    window.updateBadgeCounts();
    if (window.lucide) lucide.createIcons();
};

window.updateBadgeCounts = function() {
    const seenPending = JSON.parse(localStorage.getItem('seen_pending') || '[]');
    const seenApproved = JSON.parse(localStorage.getItem('seen_approved') || '[]');

    // Only count as "NEW" if created/updated in the last 2 hours AND not seen
    const twoHoursAgo = Math.floor(Date.now() / 1000) - (2 * 60 * 60);

    const pendingRows = document.querySelectorAll('tr[data-type="pending"]');
    let newPendingCount = 0;
    pendingRows.forEach(row => {
        const id = row.getAttribute('data-id');
        const ts = parseInt(row.getAttribute('data-timestamp') || '0');
        if (!seenPending.includes(id) && ts > twoHoursAgo) {
            newPendingCount++;
        } else if (!seenPending.includes(id)) {
            // Auto-mark very old items as seen if they are cluttering the badge
            seenPending.push(id);
            localStorage.setItem('seen_pending', JSON.stringify(seenPending));
        }
    });

    const approvedRows = document.querySelectorAll('tr[data-type="approved"]');
    let newApprovedCount = 0;
    approvedRows.forEach(row => {
        const id = row.getAttribute('data-id');
        const ts = parseInt(row.getAttribute('data-timestamp') || '0');
        if (!seenApproved.includes(id) && ts > twoHoursAgo) {
            newApprovedCount++;
        } else if (!seenApproved.includes(id)) {
            seenApproved.push(id);
            localStorage.setItem('seen_approved', JSON.stringify(seenApproved));
        }
    });

    const pBadge = document.querySelector('#btn-pending .count-badge');
    const aBadge = document.querySelector('#btn-approved .count-badge');

    if (pBadge) {
        pBadge.innerText = newPendingCount;
        pBadge.style.display = newPendingCount > 0 ? 'inline-block' : 'none';
    }
    if (aBadge) {
        aBadge.innerText = newApprovedCount;
        aBadge.style.display = newApprovedCount > 0 ? 'inline-block' : 'none';
    }
}

window.processHighlights = function(type) {
    const seenKey = type === 'pending' ? 'seen_pending' : 'seen_approved';
    const seenIds = JSON.parse(localStorage.getItem(seenKey) || '[]');
    const rows = document.querySelectorAll(`tr[data-type="${type}"]`);
    const twoHoursAgo = Math.floor(Date.now() / 1000) - (2 * 60 * 60);
    
    rows.forEach(row => {
        const id = row.getAttribute('data-id');
        const ts = parseInt(row.getAttribute('data-timestamp') || '0');
        
        // Only highlight if not seen AND reasonably recent (2 hours)
        if (!seenIds.includes(id) && ts > twoHoursAgo) {
            row.classList.add('row-highlight-new');
            const badge = row.querySelector('.row-new-badge');
            if (badge) {
                badge.style.display = 'block';
                badge.style.opacity = '1';
            }

            // Mark as seen after 5 seconds of being visible
            setTimeout(() => {
                row.classList.remove('row-highlight-new');
                if (badge) {
                    badge.style.opacity = '0';
                    setTimeout(() => { badge.style.display = 'none'; }, 500);
                }
                
                const currentSeen = JSON.parse(localStorage.getItem(seenKey) || '[]');
                if (!currentSeen.includes(id)) {
                    currentSeen.push(id);
                    localStorage.setItem(seenKey, JSON.stringify(currentSeen));
                }
                updateBadgeCounts();
            }, 5000); // 5 seconds highlight
        }
    });
}

window.liveSearch = function(query) {
    const searchVal = query.toLowerCase().trim();
    
    // 1. INSTANT DOM FILTER: Hide/show existing rows first
    const rows = document.querySelectorAll('.tr-row:not(.empty-row)');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchVal) ? 'table-row' : 'none';
    });

    // 2. BACKGROUND REFRESH: Debounce the server-side pagination fetch
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        window.fetchRequestsData(1, query);
    }, 200);
}

window.fetchRequestsPage = function(page) {
    const searchInput = document.getElementById('liveSearchInput');
    const query = searchInput ? searchInput.value : '';
    window.fetchRequestsData(page, query);
}

window.fetchRequestsData = function(page = 1, query = undefined) {
    const searchInput = document.getElementById('liveSearchInput');
    if (query === undefined) query = searchInput ? searchInput.value : '';

    const tabInput = document.querySelector('input[name="tab"]');
    const tab = tabInput ? tabInput.value : 'pending';
    
    const url = new URL(window.location.href);
    url.searchParams.set('tab', tab);
    url.searchParams.set('search', query);
    
    // Page param is tab-specific: pending_page, approved_page
    const pageParam = tab + '_page';
    url.searchParams.set(pageParam, page);

    fetch(url.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        const newTable = doc.getElementById('tab-' + tab);
        const currentTable = document.getElementById('tab-' + tab);
        
        if (newTable && currentTable) {
            currentTable.innerHTML = newTable.innerHTML;
            window.processHighlights(tab);
        }

        window.history.replaceState({}, '', url.toString());
        if (window.lucide) lucide.createIcons();
    })
    .catch(error => console.error('Pagination fetch failed:', error));
}



window.toggleFilterMenu = function() {
    const menu = document.getElementById('filterMenu');
    const btn = document.querySelector('.filter-toggle-btn');
    if (!menu || !btn) return;
    const chevron = btn.querySelector('.chevron');
    const isVisible = menu.style.display === 'block';

    menu.style.display = isVisible ? 'none' : 'block';
    btn.classList.toggle('active', !isVisible);
    if (chevron) chevron.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(180deg)';
}

// Close filter menu when clicking outside
window.addEventListener('click', (e) => {
    const menu = document.getElementById('filterMenu');
    const btn = document.querySelector('.filter-toggle-btn');
    if (menu && btn && !menu.contains(e.target) && !btn.contains(e.target)) {
        menu.style.display = 'none';
        btn.classList.remove('active');
        const chevron = btn.querySelector('.chevron');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }
});

window.approveWithPreparedBy = function() {
    const iframe = document.getElementById('detailsFrame');
    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
    const preparedByNameEl = iframeDoc.querySelector('.editable-name');
    const preparedByName = preparedByNameEl ? preparedByNameEl.innerText.trim() : '';
    
    const form = document.getElementById('form-approve');
    
    let input = form.querySelector('input[name="prepared_by"]');
    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'prepared_by';
        form.appendChild(input);
    }
    input.value = (preparedByName === '(Click to edit)' || preparedByName === '') ? '' : preparedByName;
    
    showConfirmModal('approve', 'form-approve');
}

window.rejectRequestWithConfirm = function() {
    showConfirmModal('reject', 'form-reject');
}

let currentConfirmFormId = null;
window.showConfirmModal = function(type, formId) {
    currentConfirmFormId = formId;
    const modal = document.getElementById('confirmActionModal');
    const iconBox = document.getElementById('confirmIconBox');
    const icon = document.getElementById('confirmIcon');
    const title = document.getElementById('confirmTitle');
    const message = document.getElementById('confirmMessage');
    const submitBtn = document.getElementById('confirmSubmitBtn');

    if (!modal || !iconBox || !icon || !title || !message || !submitBtn) return;

    if (type === 'approve') {
        iconBox.style.background = '#dcfce7';
        iconBox.style.color = '#10b981';
        icon.setAttribute('data-lucide', 'check-circle-2');
        title.innerText = 'Approve Request';
        message.innerText = 'Are you sure you want to approve this request? The status will be updated immediately.';
        submitBtn.style.background = '#10b981';
        submitBtn.style.color = 'white';
        submitBtn.innerText = 'Approve';
    } else {
        iconBox.style.background = '#fee2e2';
        iconBox.style.color = '#ef4444';
        icon.setAttribute('data-lucide', 'x-circle');
        title.innerText = 'Reject Request';
        message.innerText = 'Are you sure you want to reject this request? This action cannot be undone.';
        submitBtn.style.background = '#ef4444';
        submitBtn.style.color = 'white';
        submitBtn.innerText = 'Reject';
    }
    
    document.addEventListener('keydown', handleConfirmEnter);
    
    modal.classList.add('active');
    if (window.lucide) lucide.createIcons();
    setTimeout(() => submitBtn.focus(), 100);
}

function handleConfirmEnter(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        submitConfirmForm();
    }
}

window.closeConfirmModal = function() {
    const modal = document.getElementById('confirmActionModal');
    if (modal) modal.classList.remove('active');
    document.removeEventListener('keydown', handleConfirmEnter);
    currentConfirmFormId = null;
}

window.submitConfirmForm = function() {
    if (currentConfirmFormId) {
        const form = document.getElementById(currentConfirmFormId);
        if (form) {
            form.submit();
        } else {
            console.error('[Real-time] Form not found during submission:', currentConfirmFormId);
            // Fallback: if it's a quick-approve form, it might have been refreshed. 
            // The record still exists so we try to find the new form element
            const newForm = document.getElementById(currentConfirmFormId);
            if (newForm) newForm.submit();
        }
    }
}

window.showRequestDetails = function(data) {
    document.getElementById('detailsFrame').src = `/portal/view/${data.id}?compact=1`;

    const modalFooter = document.getElementById('modal-approval-footer');
    const printBtn = document.getElementById('print-btn-modal');

    if (data.status === 'approved') {
        if (modalFooter) modalFooter.style.display = 'none';
        if (printBtn) printBtn.style.display = 'none';
    } else {
        if (modalFooter) modalFooter.style.display = 'block';
        if (printBtn) printBtn.style.display = 'block';
        
        if (document.getElementById('form-approve')) {
            document.getElementById('form-approve').action = `/requests/${data.id}/approve`;
        }
        if (document.getElementById('form-reject')) {
            document.getElementById('form-reject').action = `/requests/${data.id}/reject`;
        }
    }

    document.getElementById('detailsModal').classList.add('active');
    if (window.lucide) lucide.createIcons();
}

window.printIframe = function() {
    const frame = document.getElementById('detailsFrame');
    if (frame.contentWindow) {
        frame.contentWindow.print();
    }
}

window.showAttachment = function(url) {
    const frame = document.getElementById('attachmentFrame');
    const imgContainer = document.getElementById('attachmentImageContainer');
    const img = document.getElementById('attachmentImage');

    const isImage = /\.(jpg|jpeg|png|webp|gif|svg)$/i.test(url);

    if (isImage) {
        frame.style.display = 'none';
        imgContainer.style.display = 'flex';
        img.src = url;
    } else {
        imgContainer.style.display = 'none';
        frame.style.display = 'block';
        frame.src = url;
    }

    document.getElementById('attachmentModal').classList.add('active');
}

window.closeModal = function(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('active');
    if (id === 'attachmentModal') {
        document.getElementById('attachmentFrame').src = '';
        document.getElementById('attachmentImage').src = '';
    }
}

window.closeToast = function() {
    const toast = document.getElementById('successToast');
    if (toast) {
        toast.style.animation = 'toastSlideOut 0.3s ease-in forwards';
        setTimeout(() => toast.remove(), 300);
    }
}

// Global window click to close modals
window.onclick = function (event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.classList.remove('active');
        if (event.target.id === 'attachmentModal') {
            document.getElementById('attachmentFrame').src = '';
            document.getElementById('attachmentImage').src = '';
        }
    }
}
