@extends('layouts.app')

@section('title', 'My Profile')

@push('styles')
<style>
/* Modern Profile Layout */
.profile-wrapper {
    display: grid;
    grid-template-columns: 480px 1fr;
    gap: 2rem;
    padding: 0 2.5rem;
    margin-top: 2rem;
}

.profile-header-pad {
    padding: 0 2.5rem;
}

@media (max-width: 1024px) {
    .profile-wrapper {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .profile-wrapper {
        padding: 0 1.25rem;
        gap: 1.5rem;
        display: flex;
        flex-direction: column;
        width: 100%;
        box-sizing: border-box;
    }
    .profile-header-pad {
        padding: 1.5rem 1.25rem 0.5rem !important;
        width: 100%;
        box-sizing: border-box;
    }
    .settings-header {
        padding: 1.25rem 1.5rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    .settings-body {
        padding: 1.5rem;
    }
}

.profile-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 0 0 1.5rem;
    text-align: center;
    box-shadow: var(--shadow-md);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.profile-card-header {
    height: 130px;
    background: linear-gradient(135deg, #7c3aed 0%, #3b82f6 100%);
    position: relative;
}
.profile-card-header::after {
    content: '';
    position: absolute;
    bottom: -60px;
    left: -20%;
    right: -20%;
    height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
}

.profile-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.profile-avatar-wrapper {
    position: relative;
    width: 120px;
    height: 120px;
    margin: -60px auto 1.25rem;
    border-radius: 50%;
    padding: 5px;
    background: var(--bg-card);
    z-index: 2;
}

.profile-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    background: var(--bg-main);
}

.profile-avatar-fallback {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    font-weight: 800;
    border: 4px solid var(--bg-card);
}

.avatar-upload-btn {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #1e293b;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 4px solid var(--bg-card);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: var(--shadow-md);
}

.avatar-upload-btn:hover {
    transform: scale(1.1) rotate(10deg);
}

.profile-name {
    font-family: 'Outfit', sans-serif;
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 0.25rem;
}

.profile-role {
    font-size: 1rem;
    color: var(--text-muted);
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 1rem;
    background: var(--bg-main);
    border-radius: 9999px;
    border: 1px solid var(--border-light);
}

.settings-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
}

.settings-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--border-light);
    background: var(--bg-main);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.settings-header i {
    color: var(--primary);
}

.settings-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-main);
    margin: 0;
}

.settings-body {
    padding: 2.5rem 2rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.privacy-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: start;
}

@media (max-width: 768px) {
    .privacy-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-size: 0.8125rem;
    font-weight: 700;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-control {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: var(--bg-main);
    color: var(--text-main);
    font-family: inherit;
    font-size: 0.9375rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-soft);
    background: var(--bg-card);
}

.btn-update {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2.5rem;
    background: var(--primary-gradient);
    color: white;
    border: none;
    border-radius: 14px;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
}

.btn-update:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
}

.divider {
    height: 1px;
    background: var(--border-light);
    margin: 3rem 0;
    position: relative;
}

.file-input-hidden {
    display: none;
}

/* Animations */
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}

.animate-slide-up {
    animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    opacity: 0;
    transform: translateY(30px);
}

.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Toast Notifications */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.toast {
    background: var(--bg-card);
    border-radius: 12px;
    padding: 1rem 1.5rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    font-size: 0.875rem;
    color: var(--text-main);
    border-left: 4px solid var(--primary);
    transform: translateX(120%);
    opacity: 0;
    animation: toastSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.toast-success { border-left-color: #10b981; }
.toast-success i { color: #10b981; }

.toast-error { border-left-color: #ef4444; }
.toast-error i { color: #ef4444; }

.toast.hiding {
    animation: toastSlideOut 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes toastSlideIn {
    from { transform: translateX(120%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes toastSlideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(120%); opacity: 0; }
}

/* Password field indicators */
.pw-indicator {
    font-size: 0.72rem;
    font-weight: 600;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    min-height: 18px;
    transition: all 0.2s;
}
.pw-indicator.valid  { color: #10b981; }
.pw-indicator.invalid { color: #ef4444; }
.pw-indicator.checking { color: var(--text-muted); }
.pw-indicator svg { width: 13px; height: 13px; flex-shrink: 0; }

/* Success mini modal */
.success-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
    animation: fadeIn 0.2s ease;
}
.success-modal-box {
    background: var(--bg-card);
    border-radius: 20px;
    padding: 2.5rem 3rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    text-align: center;
    animation: popIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    min-width: 280px;
}
.success-modal-icon {
    width: 60px;
    height: 60px;
    background: #d1fae5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}
.success-modal-icon svg { width: 30px; height: 30px; color: #10b981; }
.success-modal-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 0.35rem;
}
.success-modal-sub {
    font-size: 0.875rem;
    color: var(--text-muted);
}
.success-modal-bar {
    height: 4px;
    background: #10b981;
    border-radius: 2px;
    margin-top: 1.5rem;
    animation: shrink 2s linear forwards;
    transform-origin: left;
}
@keyframes shrink {
    from { transform: scaleX(1); }
    to   { transform: scaleX(0); }
}
@keyframes popIn {
    from { opacity: 0; transform: scale(0.8); }
    to   { opacity: 1; transform: scale(1); }
}
</style>
@endpush

@section('content')
@php
    $viewer = \App\Models\User::find(session('auth_user_id'));
    $viewer_role = $viewer ? $viewer->role : 'staff';
    $isReadOnly = $isReadOnly ?? false;
    $isOwnProfile = (session('auth_user_id') == $user->id);
    // Allow admin to edit others
    $canEdit = ($isOwnProfile || $viewer_role === 'admin');
@endphp
    <div style="padding: 1rem 2.5rem 0.5rem; display: flex; align-items: center; justify-content: space-between;">
        @if(!$isOwnProfile)
        <a href="{{ route('admin.users.index') }}" class="btn-return-arrow" style="width: 42px; height: 42px; border-radius: 50%; background: var(--bg-card); border: 1px solid var(--border-light); color: var(--text-main); display: flex; align-items: center; justify-content: center; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: var(--shadow-sm); text-decoration: none;" onmouseover="this.style.boxShadow='var(--shadow-md)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='var(--shadow-sm)'; this.style.transform='translateY(0)';">
            <i data-lucide="arrow-left" style="width: 20px; height: 20px;"></i>
        </a>
        @else
        <div style="width: 42px;"></div> {{-- Spacer to match alignment --}}
        @endif
    </div>

    <div class="page-header profile-header-pad" style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: -0.5rem;">
        <div>
            <h1 class="page-title" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 2rem; color: var(--text-main); margin-bottom: 0.5rem;">
                {{ $isOwnProfile ? 'My Profile' : ($user->name . "'s Profile") }}
            </h1>
            <p class="page-subtitle" style="color: var(--text-muted); font-size: 1rem;">
                {{ $isOwnProfile ? 'Manage your personal information and security settings' : 'Manage account roles, permissions and security' }}
            </p>
        </div>
    </div>

<div class="toast-container" id="toastContainer">
    @if(session('success'))
    <div class="toast toast-success">
        <i data-lucide="check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="toast toast-error">
        <i data-lucide="alert-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="toast toast-error">
        <i data-lucide="alert-circle"></i>
        <span>Please check the form below for errors.</span>
    </div>
    @endif
</div>

<div class="profile-wrapper">
    <!-- Left Column Group -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem; height: 100%; min-height: 0;">
        <!-- Left Sidebar: Avatar & Quick Info -->
        <div class="profile-card animate-slide-up">
            <div class="profile-card-header"></div>
            <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatarForm" style="padding: 0 1.5rem;">
            @csrf
            <div class="profile-avatar-wrapper" id="avatar-container">
                @if($user->profile_picture_content)
                    <img src="{{ route('display.user-avatar', ['id' => $user->id]) }}" alt="Profile Picture" class="profile-avatar" id="avatar-preview">
                @elseif($user->profile_picture)
                    <img src="{{ asset($user->profile_picture) }}" alt="Profile Picture" class="profile-avatar" id="avatar-preview">
                @else
                    <div class="profile-avatar-fallback" id="avatar-fallback">
                        {{ $user->initials() }}
                    </div>
                    <img src="" alt="Profile Picture" class="profile-avatar" id="avatar-preview" style="display: none;">
                @endif
                
                @if($canEdit)
                <label for="avatar-input" class="avatar-upload-btn" title="Change Profile Picture">
                    <i data-lucide="camera" style="width: 16px; height: 16px;"></i>
                </label>
                <input type="file" name="avatar" id="avatar-input" class="file-input-hidden" accept="image/*" onchange="previewAvatar(this)">
                @endif
            </div>

            <h2 class="profile-name" style="font-size: 1.5rem; color: var(--text-main);">{{ $user->name }}</h2>
            <div class="profile-role" style="font-size: 0.875rem; padding: 0.2rem 0.75rem; background: var(--bg-main); border: 1px solid var(--border-light); color: var(--text-muted);">
                <i data-lucide="{{ $user->role === 'admin' ? 'shield-check' : 'user' }}" style="width: 14px; height: 14px; color: var(--primary);"></i>
                {{ ucfirst($user->role) }}
            </div>
            
            <p style="color: var(--text-muted); font-size: 0.8125rem; margin-top: 1rem; margin-bottom: 0;">
                Joined {{ $user->created_at->format('F d, Y') }}
            </p>
            
            <button type="submit" id="save-avatar-btn" class="btn-update" style="display: none; width: 100%; margin-top: 1.5rem; justify-content: center; padding: 0.75rem;">
                <i data-lucide="save"></i> Save Picture
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 1.5rem; border-top: 1px solid var(--border-light); padding-top: 1rem;">
            <div style="font-size: 0.8125rem; color: var(--text-muted);">Role: <span style="color: var(--text-main); font-weight: 600;">{{ ucfirst($user->role) }}</span></div>
        </div>

    </div>
    
    <!-- Recent Activity Side Panel under profile card -->
    <div class="settings-card animate-slide-up delay-1" style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; font-family: Outfit; font-weight: 700; flex-shrink: 0;">
            <i data-lucide="history" style="width: 18px; color: var(--primary);"></i> Recent Activity
        </div>
        
        <div style="max-height: 550px; overflow-y: auto; padding-right: 0.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
            @forelse($logs as $log)
            <div style="border-left: 2px solid {{ $log->action === 'delete' ? '#ef4444' : ($log->action === 'edit' || $log->action === 'upload' ? '#3b82f6' : 'var(--primary)') }}; padding-left: 1.25rem; position: relative; flex-shrink: 0;">
                <div style="position: absolute; left: -6px; top: 0; width: 10px; height: 10px; border-radius: 50%; background: {{ $log->action === 'delete' ? '#ef4444' : ($log->action === 'edit' || $log->action === 'upload' ? '#3b82f6' : 'var(--primary)') }}; border: 2px solid var(--bg-card);"></div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                    <span style="font-size: 0.75rem; color: var(--text-muted);">
                        {{ $log->created_at->diffForHumans() }}
                    </span>
                    <span style="font-size: 0.65rem; color: var(--text-muted); opacity: 0.7;">
                        {{ $log->created_at->format('M d, H:i') }}
                    </span>
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    @php
                        $icon = match($log->action) {
                            'login' => 'log-in',
                            'view' => 'eye',
                            'edit' => 'edit-3',
                            'delete' => 'trash-2',
                            'export' => 'download',
                            'upload' => 'upload-cloud',
                            'create' => 'user-plus',
                            default => 'activity'
                        };
                        $color = match($log->action) {
                            'delete' => '#ef4444',
                            'edit', 'upload' => '#3b82f6',
                            'login', 'create' => '#10b981',
                            'export' => '#8b5cf6',
                            default => 'var(--primary)'
                        };
                    @endphp
                    <i data-lucide="{{ $icon }}" style="width: 14px; height: 14px; color: {{ $color }};"></i>
                    <span style="font-size: 0.8125rem; font-weight: 700; color: var(--text-main); text-transform: capitalize;">
                        {{ $log->action }} ({{ ucfirst($log->module) }})
                    </span>
                </div>

                <div style="font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; opacity: 0.9;">
                    {{ $log->description ?: 'No detail provided' }}
                    @if($log->ip_address)
                    <div style="font-size: 0.65rem; opacity: 0.6; margin-top: 2px;">
                        IP: {{ $log->ip_address }}
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div style="font-size: 0.8125rem; color: var(--text-muted); text-align: center; padding: 2rem 0;">No activity logs found.</div>
            @endforelse
        </div>
    </div>
    </div>

    <!-- Right Content: Forms & Stats -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem; height: 100%;">
        
        <!-- Login Frequency Chart -->
        <div class="settings-card">
            <div class="settings-header" style="justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <i data-lucide="activity" style="color: var(--primary);"></i>
                    <h3 class="settings-title">Login Frequency</h3>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem;">
                    <span style="background: var(--success-soft); color: #10b981; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700;">Online</span>
                    <span style="background: var(--bg-main); color: var(--text-muted); padding: 0.25rem 0.75rem; border-radius: 9999px; border: 1px solid var(--border-light);">Last login: {{ $logs->first() ? $logs->first()->created_at->format('Y-m-d H:i:s') : 'N/A' }}</span>
                    <span style="background: var(--bg-main); color: var(--text-muted); padding: 0.25rem 0.75rem; border-radius: 9999px; border: 1px solid var(--text-muted);">Total logins: {{ $totalLogins }}</span>
                </div>
            </div>
            <div class="settings-body" style="padding: 1.5rem; height: 180px; position: relative;">
                <canvas id="loginChart"></canvas>
            </div>
        </div>

        @if(!$isOwnProfile && $viewer_role === 'admin')
        <!-- Admin Management Section (Editing another user) -->
        <div class="settings-card animate-slide-up delay-2" style="flex: 1; display: flex; flex-direction: column;">
            <div class="settings-header" style="flex-direction: row; align-items: center;">
                <i data-lucide="settings-2"></i>
                <h3 class="settings-title">Account Management</h3>
            </div>
            <div class="settings-body" style="padding: 1.5rem 2rem;">
                <form action="{{ route('admin.users.update-from-profile', $user->id) }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>New Password (Optional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                        </div>
                        <div class="form-group">
                            <label>Account Role</label>
                            <select name="role" id="profile_role" onchange="togglePermissionsProfile()" class="form-control" style="background: var(--bg-main);">
                                <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff (Limited Access)</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator (Full Access)</option>
                            </select>
                        </div>
                    </div>

                    <div id="permissions_section_profile" style="margin-top: 1rem; {{ $user->role === 'admin' ? 'display: none;' : '' }}">
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.75rem; text-transform: uppercase;">PERMISSIONS</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            @foreach($available_permissions as $key => $label)
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-light); border-radius: 8px; font-size: 0.85rem; color: var(--text-main);">
                                <input type="checkbox" name="permissions[]" value="{{ $key }}" 
                                    {{ in_array($key, $user->permissions ?? []) ? 'checked' : '' }}
                                    style="width: 16px; height: 16px; accent-color: var(--primary);">
                                {{ $label }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 2rem; justify-content: flex-end;">
                        <button type="button" onclick="window.location.reload()" style="padding: 0.6rem 2rem; background: transparent; border: none; color: var(--text-main); font-weight: 600; cursor: pointer;">Cancel</button>
                        <button type="submit" class="btn-update" style="padding: 0.6rem 2rem; border-radius: 8px;">
                            <i data-lucide="save"></i> Update Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @elseif($isOwnProfile)
        <!-- Privacy & Security (Self Viewing) -->
        <div class="settings-card animate-slide-up delay-2" style="flex: 1; display: flex; flex-direction: column;">
            <div class="settings-header" style="flex-direction: row; align-items: center;">
                <i data-lucide="shield"></i>
                <h3 class="settings-title">Privacy & Security</h3>
            </div>
            <div class="settings-body" style="padding: 1.5rem 2rem;">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="privacy-grid">
                        <!-- Email Section -->
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase;">Email</label>
                            <div class="form-group" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0;">
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" style="flex: 1; padding: 0.6rem; border-radius: 6px;" required>
                                <input type="hidden" name="name" value="{{ $user->name }}">
                            </div>
                            @error('email')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <!-- Password Section -->
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.5rem;">Password Management</label>
                            
                            <button type="button" onclick="openPasswordModal()" style="width: 100%; padding: 0.75rem; background: transparent; border: 1px solid var(--primary); color: var(--primary); border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s;">
                                <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Change Password
                            </button>

                            <div style="display: flex; gap: 1rem; margin-top: 1.5rem; justify-content: flex-end;">
                                <button type="button" onclick="window.location.reload()" style="padding: 0.6rem 2rem; background: transparent; border: none; color: var(--text-main); font-weight: 600; cursor: pointer;">Cancel</button>
                                <button type="submit" style="padding: 0.6rem 2rem; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @else
        <!-- Information Section (Admin viewing someone else, but only if logic above fails for some reason or Fallback) -->
        <div class="settings-card animate-slide-up delay-2" style="flex: 1; display: flex; flex-direction: column;">
            <div class="settings-header" style="flex-direction: row; align-items: center;">
                <i data-lucide="info"></i>
                <h3 class="settings-title">Account Information</h3>
            </div>
            <div class="settings-body" style="padding: 1.5rem 2rem;">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Email Address</label>
                        <div class="form-control" style="background: var(--bg-card); cursor: default;">{{ $user->email }}</div>
                    </div>
                    <div class="form-group">
                        <label>Current Status</label>
                        <div class="form-control" style="background: var(--bg-card); cursor: default; border-color: #10b981; color: #10b981; font-weight: 700;">ACTIVE</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Password Change Modal -->
<div id="passwordModal" style="display: {{ $errors->has('current_password') || $errors->has('password') ? 'flex' : 'none' }}; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div id="passwordModalBox" style="background: var(--bg-card); width: 100%; max-width: 450px; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-xl); max-height: 90vh; overflow-y: auto;">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-family: Outfit; font-weight: 700;">Change Password</h3>
            <button onclick="closePasswordModal()" style="background: none; border: none; cursor: pointer; color: var(--text-muted);"><i data-lucide="x"></i></button>
        </div>
        <form id="passwordForm" style="padding: 1.5rem;">
            @csrf
            <div class="form-group" style="position: relative;">
                <label>Current Password</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" name="current_password" id="cur_pw" class="form-control" required style="padding-right: 2.5rem;" oninput="scheduleCurrentPwCheck()">
                    <button type="button" onclick="togglePw('cur_pw', 'cur_eye'); event.stopPropagation();" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 0; display: flex;"><svg id="cur_eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"></circle></svg></button>
                </div>
                <div id="cur_pw_indicator" class="pw-indicator"></div>
            </div>
            <div class="form-group" style="position: relative;">
                <label>New Password</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" name="password" id="new_pw" class="form-control" required style="padding-right: 2.5rem;" oninput="checkPasswordMatch()">
                    <button type="button" onclick="togglePw('new_pw', 'new_eye'); event.stopPropagation();" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 0; display: flex;"><svg id="new_eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"></circle></svg></button>
                </div>
                <div id="pw_error" class="pw-indicator"></div>
            </div>
            <div class="form-group" style="position: relative;">
                <label>Confirm Password</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" name="password_confirmation" id="conf_pw" class="form-control" required style="padding-right: 2.5rem;" oninput="checkPasswordMatch()">
                    <button type="button" onclick="togglePw('conf_pw', 'conf_eye'); event.stopPropagation();" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 0; display: flex;"><svg id="conf_eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"></circle></svg></button>
                </div>
                <div id="match_indicator" class="pw-indicator"></div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                <button type="button" onclick="closePasswordModal()" style="padding: 0.5rem 1rem; border: none; background: transparent; cursor: pointer; color: var(--text-main);">Cancel</button>
                <button type="button" id="updatePwBtn" onclick="submitPasswordForm()" style="padding: 0.5rem 1.25rem; border: none; background: var(--primary); color: white; border-radius: 6px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 0.4rem;">
                    <i data-lucide="lock" style="width:15px;height:15px;"></i> Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success Mini Modal -->
<div id="successModal" style="display:none;" class="success-modal-overlay">
    <div class="success-modal-box">
        <div class="success-modal-icon">
            <i data-lucide="check" style="width:30px;height:30px;color:#10b981;"></i>
        </div>
        <div class="success-modal-title">Password Updated!</div>
        <div class="success-modal-sub">Your password has been changed successfully.</div>
        <div class="success-modal-bar"></div>
    </div>
</div>

@push('scripts')
<script>
    function togglePermissionsProfile() {
        const role = document.getElementById('profile_role').value;
        const section = document.getElementById('permissions_section_profile');
        if (role === 'admin') {
            section.style.display = 'none';
        } else {
            section.style.display = 'block';
        }
    }

    // ─── Chart Labels with full dates ────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('loginChart').getContext('2d');
        const labels = {!! json_encode($chartLabels) !!};
        const fullDates = {!! json_encode($chartFullDates ?? $chartLabels) !!};
        const data = {!! json_encode($chartData) !!};

        const isDark = document.body.getAttribute('data-theme') === 'dark';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';
        const textColor = isDark ? '#94a3b8' : '#64748b';

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Logins',
                    data: data,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#ffffff',
                        titleColor: isDark ? '#f8fafc' : '#1e293b',
                        bodyColor: isDark ? '#94a3b8' : '#64748b',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            title: function(items) {
                                // Show full date (e.g. "Mon, Mar 10") as tooltip title
                                const idx = items[0].dataIndex;
                                return fullDates[idx];
                            },
                            label: function(context) {
                                return context.parsed.y + ' login(s)';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: textColor, font: { size: 11 } },
                        grid: { color: gridColor, drawBorder: false },
                        border: { display: false }
                    },
                    x: {
                        ticks: { color: textColor, font: { size: 11 } },
                        grid: { display: false },
                        border: { display: false }
                    }
                },
                interaction: { intersect: false, mode: 'index' },
            }
        });

        // Theme sync
        new MutationObserver(() => {}).observe(document.body, { attributes: true, attributeFilter: ['data-theme'] });
    });

    // ─── Toggle password visibility ──────────────────────────────────────────
    // NOTE: We deliberately avoid calling lucide.createIcons() here because it
    // replaces <i> elements with new <svg> nodes. When the click event bubbles
    // up after the replacement the original target is no longer in the DOM, so
    // the backdrop-click handler sees it as "outside the modal" and closes it.
    // Instead we swap the inline SVG paths directly.
    const EYE_OPEN_PATH  = 'M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z';
    const EYE_CLOSE_PATH = 'M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24';
    const EYE_CIRCLE     = '<circle cx="12" cy="12" r="3"></circle>';
    const SLASH_LINE     = '<line x1="1" y1="1" x2="23" y2="23"></line>';

    function togglePw(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            // Render eye-off  inline
            icon.outerHTML = `<svg id="${iconId}" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                <path d="${EYE_CLOSE_PATH}"/>${SLASH_LINE}</svg>`;
        } else {
            input.type = 'password';
            // Render eye inline
            icon.outerHTML = `<svg id="${iconId}" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                <path d="${EYE_OPEN_PATH}"/>${EYE_CIRCLE}</svg>`;
        }
    }

    // ─── Current Password Real-time Check ────────────────────────────────────
    let curPwTimer = null;
    function scheduleCurrentPwCheck() {
        clearTimeout(curPwTimer);
        const val = document.getElementById('cur_pw').value;
        const ind = document.getElementById('cur_pw_indicator');
        if (!val) { ind.innerHTML = ''; return; }
        ind.className = 'pw-indicator checking';
        ind.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Checking...';
        curPwTimer = setTimeout(() => checkCurrentPassword(val), 600);
    }

    function checkCurrentPassword(pw) {
        const ind = document.getElementById('cur_pw_indicator');
        fetch('{{ route("profile.check-password") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ password: pw })
        })
        .then(r => r.json())
        .then(data => {
            if (data.valid) {
                ind.className = 'pw-indicator valid';
                ind.innerHTML = checkSvg() + ' Correct password';
            } else {
                ind.className = 'pw-indicator invalid';
                ind.innerHTML = xSvg() + ' Incorrect password';
            }
        })
        .catch(() => { ind.innerHTML = ''; });
    }

    // ─── New / Confirm Password Match Indicator ───────────────────────────────
    function checkPasswordMatch() {
        const np = document.getElementById('new_pw').value;
        const cp = document.getElementById('conf_pw').value;
        const ind = document.getElementById('match_indicator');
        if (!cp) { ind.innerHTML = ''; return; }
        if (np === cp) {
            ind.className = 'pw-indicator valid';
            ind.innerHTML = checkSvg() + ' Passwords match';
        } else {
            ind.className = 'pw-indicator invalid';
            ind.innerHTML = xSvg() + ' Passwords do not match';
        }
    }

    // ─── SVG helpers ──────────────────────────────────────────────────────────
    function xSvg() {
        return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
    }
    function checkSvg() {
        return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
    }

    // ─── Modal Open / Close ───────────────────────────────────────────────────
    function openPasswordModal() {
        document.getElementById('passwordModal').style.display = 'flex';
        resetPasswordForm();
    }

    function closePasswordModal() {
        document.getElementById('passwordModal').style.display = 'none';
        resetPasswordForm();
    }

    function resetPasswordForm() {
        document.getElementById('passwordForm').reset();
        document.getElementById('cur_pw_indicator').innerHTML = '';
        document.getElementById('match_indicator').innerHTML = '';
        document.getElementById('pw_error').innerHTML = '';
    }

    // Close password modal on backdrop click
    document.getElementById('passwordModal').addEventListener('click', function(e) {
        if (!document.getElementById('passwordModalBox').contains(e.target)) {
            closePasswordModal();
        }
    });

    // ─── AJAX Password Form Submit ────────────────────────────────────────────
    function submitPasswordForm() {
        const cur  = document.getElementById('cur_pw').value;
        const np   = document.getElementById('new_pw').value;
        const conf = document.getElementById('conf_pw').value;
        const btn  = document.getElementById('updatePwBtn');

        if (!cur || !np || !conf) {
            alert('Please fill in all password fields.');
            return;
        }
        if (np !== conf) {
            const ind = document.getElementById('match_indicator');
            ind.className = 'pw-indicator invalid';
            ind.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Passwords do not match';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;animation:spin 1s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Updating...';

        fetch('{{ route("profile.password") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                current_password: cur,
                password: np,
                password_confirmation: conf
            })
        })
        .then(async r => {
            const json = await r.json();
            if (r.ok && json.success) {
                closePasswordModal();
                showSuccessModal();
            } else {
                // Show errors
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="lock" style="width:15px;height:15px;"></i> Update Password';
                lucide.createIcons();
                if (json.errors) {
                    if (json.errors.current_password) {
                        const ind = document.getElementById('cur_pw_indicator');
                        ind.className = 'pw-indicator invalid';
                        ind.innerHTML = xSvg() + ' ' + json.errors.current_password[0];
                    }
                    if (json.errors.password) {
                        const pind = document.getElementById('pw_error');
                        pind.className = 'pw-indicator invalid';
                        pind.innerHTML = xSvg() + ' ' + json.errors.password[0];
                    }
                }
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="lock" style="width:15px;height:15px;"></i> Update Password';
            lucide.createIcons();
            alert('An error occurred. Please try again.');
        });
    }

    // ─── Success Mini Modal ───────────────────────────────────────────────────
    function showSuccessModal() {
        const modal = document.getElementById('successModal');
        modal.style.display = 'flex';
        lucide.createIcons();
        setTimeout(() => {
            modal.style.opacity = '0';
            modal.style.transition = 'opacity 0.4s';
            setTimeout(() => {
                modal.style.display = 'none';
                modal.style.opacity = '';
                modal.style.transition = '';
            }, 400);
        }, 2000);
    }

    // ─── Preview Avatar ───────────────────────────────────────────────────────
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview  = document.getElementById('avatar-preview');
                var fallback = document.getElementById('avatar-fallback');
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (fallback) fallback.style.display = 'none';
                document.getElementById('save-avatar-btn').style.display = 'inline-flex';
                preview.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    preview.style.transition = 'transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                    preview.style.transform = 'scale(1)';
                }, 50);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ─── Toast auto-hide ──────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        const toasts = document.querySelectorAll('.toast');
        if (toasts.length > 0) {
            setTimeout(() => {
                toasts.forEach(toast => {
                    toast.classList.add('hiding');
                    setTimeout(() => toast.remove(), 300);
                });
            }, 4000);
        }
    });

    // @keyframes spin
    const spinStyle = document.createElement('style');
    spinStyle.textContent = '@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
    document.head.appendChild(spinStyle);
</script>
@endpush
@endsection
