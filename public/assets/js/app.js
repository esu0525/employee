// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Sidebar toggle logic (Desktop collapse / Mobile drawer)
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebar-overlay');
const appContainer = document.getElementById('app-container');

if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', () => {
        if (window.innerWidth >= 1024) {
            // Desktop: Toggle collapse class
            appContainer.classList.toggle('collapsed-sidebar');
            // Save state to localStorage
            const isCollapsed = appContainer.classList.contains('collapsed-sidebar');
            localStorage.setItem('sidebar_collapsed', isCollapsed);
        } else {
            // Mobile: Toggle open class
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }
    });

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }
}

// Theme Switcher Logic
function setTheme(theme, event) {
    if (event) {
        event.stopPropagation();
    }
    
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('app-theme', theme);
    
    // Update active button
    const switcher = document.getElementById('theme-switcher-container');
    document.querySelectorAll('.theme-btn').forEach(btn => {
        btn.classList.remove('active');
        if(btn.getAttribute('data-theme-btn') === theme) {
            btn.classList.add('active');
        }
    });

    // Auto close the switcher
    if (switcher) {
        document.activeElement?.blur(); 
        switcher.classList.remove('open');
    }
}

// Exporting setTheme to global scope
window.setTheme = setTheme;

// Close theme switcher when clicking outside
document.addEventListener('click', function(event) {
    const switcher = document.getElementById('theme-switcher-container');
    if (switcher && switcher.classList.contains('open') && !switcher.contains(event.target)) {
        switcher.classList.remove('open');
    }
});

// Initialize Theme from Storage or Default to Light
const savedTheme = localStorage.getItem('app-theme') || 'light';
setTheme(savedTheme, null);

// Welcome Modal Functions
function closeWelcomeModal() {
    const modal = document.getElementById('welcome-modal-overlay');
    if (!modal) return;
    
    modal.style.opacity = '0';
    modal.style.visibility = 'hidden';
    document.body.classList.remove('modal-open');
    
    setTimeout(() => {
        modal.remove();
    }, 500);
}
window.closeWelcomeModal = closeWelcomeModal;

function toggleSubnav(e) {
    e.preventDefault();
    const subnav = document.getElementById('masterlistSubnav');
    const arrow = e.currentTarget.querySelector('.subnav-arrow');
    
    if (subnav.style.display === 'none') {
        subnav.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
    } else {
        subnav.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
}
window.toggleSubnav = toggleSubnav;

// Apply blur on load and set auto-close if modal exists
window.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('welcome-modal-overlay')) {
        document.body.classList.add('modal-open');
        
        // Auto close after 800ms for snappier loading
        setTimeout(() => {
            closeWelcomeModal();
        }, 800);
    }

    // Auto-dismiss success toast if it exists
    const toast = document.getElementById('successToast');
    if (toast) {
        setTimeout(() => {
            closeToast();
        }, 3000);
    }
    
    // Re-initialize icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

// Global Formatting Script
document.addEventListener('input', function(e) {
    // 1. Title Case Formatting
    const isText = (e.target.tagName === 'INPUT' && (e.target.type === 'text' || !e.target.type)) || e.target.tagName === 'TEXTAREA';
    const fieldName = e.target.name || '';
    const fieldId = e.target.id || '';
    
    // Check if the field is a password field (even if toggled to text) or explicitly in skip list
    const skipFields = ['email', 'password', 'id', 'username', 'so_no', 'so_number', 'agency', 'position'];
    const isPasswordField = fieldName.includes('password') || fieldId.includes('password') || e.target.hasAttribute('data-no-capitalize');
    const shouldSkip = skipFields.includes(fieldName) || skipFields.includes(fieldId) || isPasswordField;
    
    if (isText && !shouldSkip) {
        const cursorStart = e.target.selectionStart;
        const cursorEnd = e.target.selectionEnd;
        
        let val = e.target.value;
        if (val) {
            e.target.value = val.replace(/\w\S*/g, function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        }
        e.target.setSelectionRange(cursorStart, cursorEnd);
    }

    // 2. Phone Formatting ####-###-####
    const isPhoneField = e.target.name?.includes('phone') || e.target.id?.includes('phone');
    if (isPhoneField) {
        let val = e.target.value.replace(/\D/g, '');
        if (val.length > 11) val = val.substr(0, 11);
        
        let formatted = val;
        if (val.length > 4 && val.length <= 7) {
            formatted = val.substr(0, 4) + '-' + val.substr(4);
        } else if (val.length > 7) {
            formatted = val.substr(0, 4) + '-' + val.substr(4, 3) + '-' + val.substr(7);
        }
        e.target.value = formatted;
    }
});

function closeToast() {
    const toast = document.getElementById('successToast');
    if (toast) {
        toast.classList.remove('active');
        setTimeout(() => toast.remove(), 400);
    }
}
window.closeToast = closeToast;

// Modern Confirmation Helper
window.confirmAction = function(options = {}) {
    return new Promise((resolve) => {
        const modal = document.getElementById('confirmModal');
        const title = document.getElementById('confirmModalTitle');
        const msg = document.getElementById('confirmModalMessage');
        const cancelBtn = document.getElementById('confirmModalCancel');
        const proceedBtn = document.getElementById('confirmModalProceed');
        const iconBox = document.getElementById('confirmIconBox');

        if (!modal) {
            // Fallback to native if modal not found
            resolve(confirm(options.message || 'Are you sure?'));
            return;
        }

        title.innerText = options.title || 'Are you sure?';
        msg.innerText = options.message || 'This action cannot be undone.';
        proceedBtn.innerText = options.confirmText || 'Confirm';
        cancelBtn.innerText = options.cancelText || 'Cancel';
        
        if(options.type === 'danger') {
            iconBox.style.background = '#fef2f2';
            iconBox.style.color = '#ef4444';
            iconBox.style.animation = 'pulse-red 2s infinite';
            proceedBtn.style.background = '#ef4444';
            proceedBtn.style.boxShadow = '0 8px 20px -6px rgba(239, 68, 68, 0.5)';
            iconBox.innerHTML = '<i data-lucide="alert-triangle" style="width: 32px; height: 32px;"></i>';
        } else {
            iconBox.style.background = '#eff6ff';
            iconBox.style.color = '#3b82f6';
            iconBox.style.animation = 'none';
            proceedBtn.style.background = '#3b82f6';
            proceedBtn.style.boxShadow = '0 8px 20px -6px rgba(59, 130, 246, 0.5)';
            iconBox.innerHTML = '<i data-lucide="help-circle" style="width: 32px; height: 32px;"></i>';
        }
        
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('active'), 10);

        const handleProceed = () => { cleanup(); resolve(true); };
        const handleCancel = () => { cleanup(); resolve(false); };
        const handleKeydown = (e) => {
            if (e.key === 'Enter') { e.preventDefault(); handleProceed(); }
            else if (e.key === 'Escape') { handleCancel(); }
        };
        const handleBackdropClick = (e) => { if (e.target === modal) handleCancel(); };
        const cleanup = () => {
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
            proceedBtn.removeEventListener('click', handleProceed);
            cancelBtn.removeEventListener('click', handleCancel);
            document.removeEventListener('keydown', handleKeydown);
            modal.removeEventListener('click', handleBackdropClick);
        };

        proceedBtn.addEventListener('click', handleProceed);
        cancelBtn.addEventListener('click', handleCancel);
        document.addEventListener('keydown', handleKeydown);
        modal.addEventListener('click', handleBackdropClick);
    });
};
