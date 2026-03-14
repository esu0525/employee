@extends('layouts.app')

@section('title', 'Account Management')

@section('content')
<style>
    @media (max-width: 768px) {
        .am-header {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 1rem;
        }
        .am-header button {
            width: 100%;
            justify-content: center;
        }
        .am-table-wrapper {
            overflow-x: auto;
        }
    }
</style>
<div class="page-header am-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 class="page-title">Account Management</h1>
        <p class="page-subtitle">Manage user accounts and access permissions</p>
    </div>
    <button onclick="openModal('createUserModal')" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600;">
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

<div class="card am-table-wrapper" style="background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: var(--shadow-sm);">
    <div style="overflow-x: auto;">
    <table style="width: 100%; min-width: 800px; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: var(--bg-main); border-bottom: 1px solid var(--border);">
                <th style="padding: 1rem 1.5rem; font-weight: 700; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">User</th>
                <th style="padding: 1rem 1.5rem; font-weight: 700; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Role</th>
                <th style="padding: 1rem 1.5rem; font-weight: 700; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Permissions</th>
                <th style="padding: 1rem 1.5rem; font-weight: 700; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Created</th>
                <th style="padding: 1rem 1.5rem; font-weight: 700; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" onmouseover="this.style.background='var(--primary-soft)'" onmouseout="this.style.background='transparent'">
                <td style="padding: 1rem 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 40px; height: 40px; background: {{ $user->role === 'admin' ? 'var(--primary-gradient)' : '#e2e8f0' }}; color: {{ $user->role === 'admin' ? 'white' : '#64748b' }}; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;">
                            {{ $user->initials() }}
                        </div>
                        <div>
                            <p style="font-weight: 700; color: var(--text-main); margin: 0; font-size: 0.9375rem;">{{ $user->name }}</p>
                            <p style="color: var(--text-muted); margin: 0; font-size: 0.8125rem;">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>
                <td style="padding: 1rem 1.5rem;">
                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; 
                        background: {{ $user->role === 'admin' ? 'var(--info-soft)' : 'var(--bg-main)' }}; 
                        color: {{ $user->role === 'admin' ? 'var(--primary-light)' : 'var(--text-muted)' }};">
                        <i data-lucide="{{ $user->role === 'admin' ? 'shield-check' : 'user' }}" style="width: 14px; height: 14px;"></i>
                        {{ $user->role }}
                    </span>
                </td>
                <td style="padding: 1rem 1.5rem;">
                    @if($user->role === 'admin')
                        <span style="color: #64748b; font-size: 0.8125rem; font-style: italic;">Full Access Level</span>
                    @else
                        <div style="display: flex; flex-wrap: wrap; gap: 0.25rem;">
                            @if(empty($user->permissions))
                                <span style="color: #94a3b8; font-size: 0.8125rem;">No permissions set</span>
                            @else
                                @foreach($user->permissions as $perm)
                                    <span style="background: var(--bg-main); border: 1px solid var(--border-light); color: var(--text-muted); padding: 0.125rem 0.5rem; border-radius: 6px; font-size: 0.6875rem; font-weight: 600;">
                                        {{ $available_permissions[$perm] ?? $perm }}
                                    </span>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </td>
                <td style="padding: 1rem 1.5rem; color: var(--text-muted); font-size: 0.8125rem;">
                    {{ $user->created_at->format('M d, Y') }}
                </td>
                <td style="padding: 1rem 1.5rem; text-align: right;">
                    <button onclick="editUser({{ $user->toJson() }})" style="background: none; border: none; padding: 0.5rem; cursor: pointer; color: #64748b; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'">
                        <i data-lucide="edit-3" style="width: 18px; height: 18px;"></i>
                    </button>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this account?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; padding: 0.5rem; cursor: pointer; color: #64748b; transition: color 0.2s;" onmouseover="this.style.color='#ef4444'">
                            <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

<!-- Create User Modal -->
<div id="createUserModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 100%; max-width: 500px; border-radius: 20px; box-shadow: var(--shadow-xl); overflow: hidden; animation: fadeInUp 0.3s ease-out;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #1e293b; margin: 0;">Create New Account</h3>
            <button onclick="closeModal('createUserModal')" style="background: none; border: none; cursor: pointer; color: #94a3b8;"><i data-lucide="x"></i></button>
        </div>
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

    // Initialize lucide
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endsection
