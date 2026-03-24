@extends('layouts.app')

@section('title', 'Account Management')

@section('content')
<div class="page-content">
    <div class="page-header am-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 class="page-title">Account Management</h1>
            <p class="page-subtitle">Manage user accounts and access permissions</p>
        </div>
        <button onclick="openModal('createUserModal')" class="btn btn-primary">
            <i data-lucide="user-plus" style="width: 20px; height: 20px;"></i>
            Create New Account
        </button>
    </div>

@if(session('success'))
<div style="background: #ecfdf5; color: #059669; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; border: 1px solid #d1fae5;">
    <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
    <span style="font-weight: 600;">{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div style="background: #fef2f2; color: #dc2626; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; border: 1px solid #fecaca;">
    <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
    <span style="font-weight: 600;">{{ session('error') }}</span>
</div>
@endif

<style>
    .am-column-headers {
        display: flex; 
        padding: 0.85rem 1.5rem; 
        margin-bottom: 1rem; 
        border-radius: 12px;
        font-size: 0.75rem; 
        font-weight: 800; 
        text-transform: uppercase; 
        letter-spacing: 0.1em;
        background: #ffffff;
        border: 2px solid #8292a9ff;
        color: #374151;
    }
    body[data-theme="dark"] .am-column-headers {
        background: #334155;
        border-color: #ffffff;
        color: #ffffff;
    }
    body[data-theme="night"] .am-column-headers {
        background: #ffffff;
        border-color: #4b5563;
        color: #374151;
    }
</style>

<!-- Column Headers -->
<div class="am-column-headers" style="display: flex; align-items: center; padding: 0.75rem 1.5rem; margin-bottom: 0.5rem; font-family: Outfit; font-weight: 700; font-size: 0.7rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; background: #ffffff; border: 1px solid var(--border-light); border-radius: 12px; margin-left: 20px; margin-right: 20px; gap: 1rem; box-sizing: border-box;">
    <div style="width: 240px; flex-shrink: 0;">User Detail</div>
    <div style="width: 120px; flex-shrink: 0;">Role</div>
    <div style="flex: 1; min-width: 200px;">Permissions</div>
    <div style="width: 140px; flex-shrink: 0;">Last Logged In</div>
    <div style="width: 100px; flex-shrink: 0;">Created</div>
    <div style="width: 90px; text-align: right; flex-shrink: 0;">Actions</div>
</div>

<div class="master-list-grid" style="margin-top: 0.5rem; margin-left: 20px; margin-right: 20px;">
    @foreach($users as $user)
    <div class="master-item-card" style="cursor: default; display: flex; align-items: center; padding: 1.25rem 1.5rem; margin-bottom: 1rem; background: var(--bg-card); border-radius: 20px; border: 1px solid var(--border-light); transition: all 0.3s ease; gap: 1rem; box-sizing: border-box;">
        <!-- User Detail -->
        <div class="master-card-left" style="width: 240px; border: none; padding: 0; display: flex; align-items: center; gap: 1rem; flex-shrink: 0;">
            <div class="master-avatar-3layer">
                <div class="master-avatar-ring" style="width: 50px; height: 50px;">
                    <div class="master-avatar-inner" style="background: {{ $user->role === 'admin' ? 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)' : '#f1f5f9' }};">
                        @if($user->profile_picture_content)
                            <img src="{{ route('display.user-avatar', ['id' => $user->id]) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div class="master-avatar-initials" style="color: {{ $user->role === 'admin' ? '#ffffff' : '#4f46e5' }}; font-weight: 800; font-size: 1.1rem;">
                                {{ $user->initials() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="master-info" style="border: none; padding: 0;">
                <span class="master-name" style="font-weight: 800; color: var(--text-main); font-size: 0.95rem;">{{ $user->name }}</span>
                <span class="master-sub" style="font-size: 0.75rem; color: var(--text-muted);">{{ $user->email }}</span>
            </div>
        </div>

        <!-- Role -->
        <div style="width: 120px; flex-shrink: 0;">
            <span class="badge {{ $user->role === 'admin' ? 'badge-transfer' : 'badge-others' }}" style="font-size: 0.65rem; padding: 0.35rem 0.85rem; border-radius: 999px; display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700;">
                <i data-lucide="{{ $user->role === 'admin' ? 'shield-check' : 'user' }}" style="width: 12px; height: 12px;"></i>
                {{ strtoupper($user->role) }}
            </span>
        </div>

        <!-- Permissions -->
        <div style="flex: 1; min-width: 200px;">
            @if($user->role === 'admin')
                <span style="color: #6366f1; font-size: 0.75rem; font-weight: 600; font-style: italic; opacity: 0.8;">Full Access Level</span>
            @else
                <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                    @if(empty($user->permissions))
                        <span style="color: #94a3b8; font-size: 0.75rem; opacity: 0.6;">No permissions set</span>
                    @else
                        @foreach($user->permissions as $perm)
                            <span style="background: #fef9c3; border: 1px solid #fde047; color: #854d0e; padding: 2px 8px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.02em;">
                                {{ $available_permissions[$perm] ?? $perm }}
                            </span>
                        @endforeach
                    @endif
                </div>
            @endif
        </div>

        <!-- Last Logged In -->
        <div style="width: 140px; font-size: 0.8rem; color: var(--text-main); font-weight: 600; flex-shrink: 0;">
            @if($user->last_login_at)
                <div style="display: flex; flex-direction: column;">
                    <span>{{ \Carbon\Carbon::parse($user->last_login_at)->format('M d, Y') }}</span>
                    <span style="font-size: 0.65rem; color: var(--text-muted); opacity: 0.7;">{{ \Carbon\Carbon::parse($user->last_login_at)->format('H:i') }}</span>
                </div>
            @else
                <span style="color: var(--text-muted); opacity: 0.5;">Never</span>
            @endif
        </div>

        <!-- Created -->
        <div style="width: 100px; color: var(--text-muted); font-size: 0.8rem; font-weight: 500; flex-shrink: 0;">
            {{ $user->created_at->format('M d, Y') }}
        </div>

        <!-- Actions -->
        <div class="master-card-right" style="width: 90px; text-align: right; border: none; padding: 0; flex-shrink: 0;">
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <a href="{{ route('admin.users.profile', $user->id) }}" class="btn btn-outline" style="padding: 0.5rem; min-width: 38px; height: 38px; border-radius: 12px; color: #3b82f6; border-color: #dbeafe; background: #eff6ff; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" title="Manage Account Profile">
                    <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                </a>
                @if($user->id != session('auth_user_id'))
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDeleteAccount(event, this)" class="btn btn-outline" style="padding: 0.5rem; min-width: 38px; height: 38px; border-radius: 12px; color: #ef4444; border-color: #fee2e2; background: #fef2f2; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                        <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Create User Modal -->
<div id="createUserModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 100%; max-width: 500px; border-radius: 20px; box-shadow: var(--shadow-xl); overflow: hidden; animation: fadeInUp 0.3s ease-out;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #1e293b; margin: 0;">Create New Account</h3>
            <button onclick="closeModal('createUserModal')" style="background: none; border: none; cursor: pointer; color: #94a3b8;"><i data-lucide="x"></i></button>
        </div>
        
        @if($errors->any())
        <div style="margin: 1.5rem 2rem 0; background: #fef2f2; color: #dc2626; padding: 1rem; border-radius: 12px; border: 1px solid #fecaca;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
                <span style="font-weight: 700;">Validation Error</span>
            </div>
            <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.85rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST" style="padding: 2rem;">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.5rem; color: #475569;">FULL NAME</label>
                <input type="text" name="name" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; focus: border-color: var(--primary);">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.5rem; color: #475569;">EMAIL ADDRESS</label>
                <input type="email" name="email" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 10px; outline: none;">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.5rem; color: #475569;">PASSWORD</label>
                <input type="password" name="password" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 10px; outline: none;">
                <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">Minimum 8 characters required</p>
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.5rem; color: #475569;">ACCOUNT ROLE</label>
                <select name="role" id="create_role" onchange="togglePermissions('create')" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 10px; background: white;">
                    <option value="staff">Staff (Limited Access)</option>
                    <option value="admin">Administrator (Full Access)</option>
                </select>
            </div>
            <div id="create_permissions_section" style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.75rem; color: #475569;">PERMISSIONS</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    @foreach($available_permissions as $key => $label)
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #475569; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="{{ $key }}" style="width: 16px; height: 16px; accent-color: var(--primary);">
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="button" onclick="closeModal('createUserModal')" style="flex: 1; padding: 0.75rem; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" style="flex: 2; padding: 0.75rem; border-radius: 10px; border: none; background: var(--primary-gradient); color: white; font-weight: 700; cursor: pointer;">Create Account</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 100%; max-width: 500px; border-radius: 20px; box-shadow: var(--shadow-xl); overflow: hidden; animation: fadeInUp 0.3s ease-out;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #1e293b; margin: 0;">Edit Account</h3>
            <button onclick="closeModal('editUserModal')" style="background: none; border: none; cursor: pointer; color: #94a3b8;"><i data-lucide="x"></i></button>
        </div>
        <form id="editUserForm" method="POST" style="padding: 2rem;">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.5rem; color: #475569;">FULL NAME</label>
                <input type="text" name="name" id="edit_name" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 10px; outline: none;">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.5rem; color: #475569;">EMAIL ADDRESS</label>
                <input type="email" name="email" id="edit_email" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 10px; outline: none;">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.5rem; color: #475569;">NEW PASSWORD (optional)</label>
                <input type="password" name="password" placeholder="Leave blank to keep current" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 10px; outline: none;">
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.5rem; color: #475569;">ACCOUNT ROLE</label>
                <select name="role" id="edit_role" onchange="togglePermissions('edit')" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 10px; background: white;">
                    <option value="staff">Staff (Limited Access)</option>
                    <option value="admin">Administrator (Full Access)</option>
                </select>
            </div>
            <div id="edit_permissions_section" style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.75rem; color: #475569;">PERMISSIONS</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    @foreach($available_permissions as $key => $label)
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #475569; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="{{ $key }}" class="edit-perm-checkbox" style="width: 16px; height: 16px; accent-color: var(--primary);">
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="button" onclick="closeModal('editUserModal')" style="flex: 1; padding: 0.75rem; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" style="flex: 2; padding: 0.75rem; border-radius: 10px; border: none; background: var(--primary-gradient); color: white; font-weight: 700; cursor: pointer;">Update Account</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function togglePermissions(type) {
        const role = document.getElementById(type + '_role').value;
        const section = document.getElementById(type + '_permissions_section');
        section.style.display = (role === 'admin') ? 'none' : 'block';
    }

    function editUser(user) {
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;
        
        // Reset checkboxes
        document.querySelectorAll('.edit-perm-checkbox').forEach(cb => {
            cb.checked = (user.permissions || []).includes(cb.value);
        });
        
        // Set action
        document.getElementById('editUserForm').action = "/accounts/" + user.id + "/update";
        
        togglePermissions('edit');
        openModal('editUserModal');
    }

    async function confirmDeleteAccount(event, button) {
        event.preventDefault();
        
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
    }

    // Initialize lucide
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        
        // Auto-open modal if there are errors
        @if($errors->any())
            openModal('createUserModal');
        @endif
    });
</script>
    </div>
</div>
@endsection
