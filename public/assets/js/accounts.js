/**
 * Account Management Module JavaScript
 * Handles user account creation, editing, role-based permission toggling,
 * and account deletion with confirmation.
 */

document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // Auto-open modal if there are validation errors from the server
    const hasErrors = document.querySelector('.validation-error-container');
    if (hasErrors) {
        openModal('createUserModal');
    }

    // Success Toast logic
    const toast = document.getElementById('successToast');
    if (toast) {
        setTimeout(() => toast.classList.add('active'), 100);
        setTimeout(() => {
            toast.classList.remove('active');
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }
});

window.openModal = function(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'flex';
}

window.closeModal = function(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.style.display = 'none';
    
    // Reset specific logic for Create User Modal
    if (id === 'createUserModal') {
        const roleSelect = document.getElementById('create_role');
        if (roleSelect) {
            roleSelect.value = "";
            togglePermissions('create');
        }
    }

    // Reset password visibility for both modals
    const createPass = document.getElementById('create_password');
    const editPass = document.getElementById('edit_password');
    if (createPass) {
        createPass.type = 'password';
        const icon = document.getElementById('create_password_icon');
        if (icon) icon.setAttribute('data-lucide', 'eye');
    }
    if (editPass) {
        editPass.type = 'password';
        const icon = document.getElementById('edit_password_icon');
        if (icon) icon.setAttribute('data-lucide', 'eye');
    }
    
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

// Close on outside click
window.onclick = function(event) {
    if (event.target.classList.contains('modal-backdrop')) {
        closeModal(event.target.id);
    }
}

window.togglePasswordVisibility = function(id) {
    const input = document.getElementById(id);
    const icon = document.getElementById(id + '_icon');
    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
    } else {
        input.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
    }
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

window.togglePermissions = function(type, isManualChange = false) {
    const roleEl = document.getElementById(type + '_role');
    const section = document.getElementById(type + '_permissions_section');
    if (!roleEl || !section) return;

    const role = roleEl.value;
    
    if (!role || role === 'admin' || role === 'coordinator') {
        section.style.display = 'none';
    } else {
        section.style.display = 'block';
        const checkboxes = document.querySelectorAll('.perm-checkbox-' + type + ', .edit-perm-checkbox');
        
        checkboxes.forEach(cb => {
            const label = cb.closest('label');
            const val = cb.value;
            // Filter checkboxes to only those relevant to the current type (create or edit)
            const isRelevant = (type === 'create' && cb.classList.contains('perm-checkbox-create')) || 
                               (type === 'edit' && cb.classList.contains('edit-perm-checkbox'));
            
            if (isRelevant && label) {
                if (role === 'viewer') {
                    if (val.startsWith('view_')) {
                        label.style.display = 'flex';
                        if (isManualChange) cb.checked = true;
                    } else {
                        label.style.display = 'none';
                        cb.checked = false;
                    }
                } else if (role === 'editor') {
                    if (val.startsWith('edit_') || val === 'manage_documents') {
                        label.style.display = 'flex';
                        if (isManualChange) cb.checked = true;
                    } else {
                        label.style.display = 'none';
                        cb.checked = false;
                    }
                }
            }
        });
    }
}

window.editUser = function(user) {
    const firstNameEl = document.getElementById('edit_first_name');
    const lastNameEl = document.getElementById('edit_last_name');
    const emailEl = document.getElementById('edit_email');
    const roleEl = document.getElementById('edit_role');
    const formEl = document.getElementById('editUserForm');

    if (firstNameEl) firstNameEl.value = user.first_name;
    if (lastNameEl) lastNameEl.value = user.last_name;
    if (emailEl) emailEl.value = user.email;
    if (roleEl) roleEl.value = user.role;
    
    // Reset and sets checkboxes
    document.querySelectorAll('.edit-perm-checkbox').forEach(cb => {
        cb.checked = (user.permissions || []).includes(cb.value);
    });
    
    // Set form action route
    if (formEl) formEl.action = "/accounts/" + user.id + "/update";
    
    togglePermissions('edit');
    openModal('editUserModal');
}

window.confirmDeleteAccount = async function(event, button) {
    event.preventDefault();
    
    if (window.confirmAction) {
        const confirmed = await window.confirmAction({
            title: 'Delete Account?',
            message: 'Are you sure you want to delete this account? This action cannot be undone.',
            confirmText: 'Delete Account',
            cancelText: 'Cancel',
            type: 'danger'
        });
        
        if (confirmed) {
            button.closest('form').submit();
        }
    } else {
        // Fallback to standard confirm if custom confirmAction is not available
        if (confirm('Are you sure you want to delete this account?')) {
            button.closest('form').submit();
        }
    }
}

window.closeToast = function() {
    const toast = document.getElementById('successToast');
    if (toast) {
        toast.classList.remove('active');
        setTimeout(() => toast.remove(), 400);
    }
}
