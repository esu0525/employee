<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System - @yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <!-- Lucide Icons via CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Flatpickr for advanced date selection -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        /* Welcome Modal Styles */
        .welcome-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 10000;
            background: rgba(15, 14, 26, 0.4);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .welcome-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .welcome-card {
            width: 720px;
            max-width: 95vw;
            background: #ffffff;
            border-radius: 40px;
            display: flex;
            overflow: hidden;
            box-shadow: 
                0 60px 120px -20px rgba(0, 0, 0, 0.45),
                0 0 0 1px rgba(0, 0, 0, 0.05);
            transform: translateY(60px) scale(0.85);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .welcome-modal-overlay.active .welcome-card {
            transform: translateY(0) scale(1);
        }

        /* Left Side: Vibrant Waving Section */
        .welcome-left {
            width: 260px;
            background: linear-gradient(135deg, #1e1b4b 0%, #4f46e5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .waving-hand {
            font-size: 7rem;
            animation: wave 2s infinite cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: 70% 70%;
            filter: drop-shadow(0 20px 30px rgba(0,0,0,0.4));
        }

        @keyframes wave {
            0% { transform: rotate( 0.0deg) }
           10% { transform: rotate(14.0deg) }
           20% { transform: rotate(-8.0deg) }
           30% { transform: rotate(14.0deg) }
           40% { transform: rotate(-4.0deg) }
           50% { transform: rotate(10.0deg) }
           60% { transform: rotate( 0.0deg) }
          100% { transform: rotate( 0.0deg) }
        }

        /* Right Side: User Details */
        .welcome-right {
            flex: 1;
            padding: 4rem 3.5rem;
            text-align: left;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .welcome-user-container {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .welcome-avatar-circle {
            width: 130px;
            height: 130px;
            background: #f8fafc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            font-weight: 800;
            color: #4f46e5;
            box-shadow: 0 15px 35px rgba(79, 70, 229, 0.15);
            border: 6px solid white;
            outline: 1px solid rgba(0,0,0,0.05);
            flex-shrink: 0;
            overflow: hidden;
        }

        .welcome-avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Text */
        .welcome-title {
            font-family: 'Inter', sans-serif;
            font-size: 1.125rem;
            font-weight: 500;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .welcome-user {
            font-family: 'Outfit', sans-serif;
            font-size: 2.25rem;
            font-weight: 800;
            color: #1e1b4b;
            letter-spacing: -0.01em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 320px;
        }

        /* Button */
        .btn-welcome-continue {
            width: 100%;
            height: 3.5rem;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 18px;
            font-size: 1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.3);
        }

        .btn-welcome-continue:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(79, 70, 229, 0.4);
        }

        .btn-welcome-continue svg {
            width: 20px;
            height: 20px;
            transition: transform 0.3s ease;
        }

        .btn-welcome-continue:hover svg {
            transform: translateX(4px);
        }

        /* Decorative elements */
        .welcome-decoration {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 1;
        }

        .deco-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.15;
        }

        .circle-1 {
            top: -20px;
            right: -20px;
            width: 150px;
            height: 150px;
            background: #4f46e5;
        }

        .circle-2 {
            bottom: -30px;
            left: -30px;
            width: 180px;
            height: 180px;
            background: #7c3aed;
        }

        /* Blur system bg */
        body.modal-open #app-container {
            filter: blur(12px) grayscale(0.2);
            transition: filter 0.5s ease;
        }

        /* Theme Overrides */
        body[data-theme="dark"] {
            --bg-main: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #ffffff; /* Solid white borders in dark mode per user request */
            --border-light: rgba(255, 255, 255, 0.4);
            --primary-soft: #334155; /* Lighter hover row in dark mode */
            --success-soft: #064e3b;
            --info-soft: #1e3a8a;
            --warning-soft: #78350f;
            --danger-soft: #7f1d1d;
            --glass: rgba(30, 41, 59, 0.85);
            --title-gradient: linear-gradient(to right, #818cf8, #a78bfa, #c084fc);
            background: var(--bg-main);
        }
        body[data-theme="dark"] .app-container {
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
        }

        body[data-theme="night"] {
            /* Sepia / Eye Protection Mode - Warm Cream/Yellowish White */
            --bg-main: #f2ead3;
            --bg-card: #ffffff;
            --text-main: #000000;
            --text-muted: #92400e;
            --border: #8c7662;
            --border-light: #a99480;
            --primary: #d97706;
            --primary-gradient: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            --primary-soft: #ebd6be;
            --success-soft: #ebf2eb;
            --info-soft: #e3f0f5;
            --warning-soft: #fdf5e6;
            --danger-soft: #fdebef;
            --title-gradient: linear-gradient(to right, #000000, #000000);
            --glass: rgba(255, 255, 255, 0.9);
            background: var(--bg-main);
            /* Add an overlay filter for true eye protection feel */
            filter: sepia(0.35) brightness(1) contrast(0.95);
        }
        body[data-theme="night"] .app-container {
            background: radial-gradient(circle at top left, #fdfbf7, #f2ead3);
        }

        /* Modern Tiny Toast - Top Right */
        .modern-toast-mini {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 10000;
            background: #10b981;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 14px;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            min-width: 200px;
            max-width: 320px;
            opacity: 0;
            transform: translateX(30px) scale(0.95);
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .modern-toast-mini.active {
            opacity: 1;
            visibility: visible;
            transform: translateX(0) scale(1);
        }

        .toast-mini-icon {
            display: flex; align-items: center; justify-content: center;
            width: 1.5rem; height: 1.5rem; background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
        }
        .toast-mini-icon i { width: 1rem; height: 1rem; stroke-width: 3px; }

        /* Status Badge Colors */
        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
        }
        .badge-resign { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
        .badge-retired { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        .badge-transfer { background: #e0f2fe; color: #0ea5e9; border: 1px solid #bae6fd; }
        .badge-others { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
        body[data-theme="dark"] .badge-resign { background: rgba(239, 68, 68, 0.15); color: #fca5a5; border-color: rgba(239, 68, 68, 0.3); }
        body[data-theme="dark"] .badge-retired { background: rgba(217, 119, 6, 0.15); color: #fcd34d; border-color: rgba(217, 119, 6, 0.3); }
        body[data-theme="dark"] .badge-transfer { background: rgba(14, 165, 233, 0.15); color: #7dd3fc; border-color: rgba(14, 165, 233, 0.3); }
        body[data-theme="dark"] .badge-others { background: rgba(148, 163, 184, 0.15); color: #cbd5e1; border-color: rgba(148, 163, 184, 0.3); }


        .toast-mini-content { display: flex; flex-direction: column; flex: 1; min-width: 0; }
        .toast-mini-title { font-size: 0.8125rem; font-weight: 800; letter-spacing: 0.02em; }
        .toast-mini-msg { font-size: 0.75rem; font-weight: 500; opacity: 0.9; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        
        .toast-mini-close {
            background: none; border: none; color: white; opacity: 0.6; cursor: pointer; padding: 2px; transition: 0.2s;
        }
        .toast-mini-close:hover { opacity: 1; transform: scale(1.1); }
        .toast-mini-close i { width: 14px; height: 14px; }

        /* Top Header Styles */
        .top-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2.5rem;
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 30;
            transition: var(--transition);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-left: auto;
        }

        .theme-switcher {
            display: flex;
            align-items: center;
            background: var(--bg-main);
            padding: 0.25rem;
            border-radius: var(--radius-full);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            gap: 0;
            height: 2.75rem; /* ensure fixed height so width transition is smooth */
        }

        .theme-switcher.open {
            gap: 0.25rem;
        }

        .theme-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            width: 0; /* hidden state */
            height: 2.25rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0;
            opacity: 0;
            overflow: hidden;
        }

        .theme-btn i {
            width: 1.125rem;
            height: 1.125rem;
        }

        .theme-btn.active {
            width: 2.25rem;
            opacity: 1;
            background: var(--primary-soft);
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        .theme-switcher.open .theme-btn {
            width: 2.25rem;
            opacity: 1;
            pointer-events: auto;
        }

        @media (hover: hover) {
            .theme-switcher:hover {
                gap: 0.25rem;
            }
            .theme-btn:hover {
                color: var(--text-main);
            }
            .theme-switcher:hover .theme-btn {
                width: 2.25rem;
                opacity: 1;
                pointer-events: auto;
            }
            .theme-switcher.open .theme-btn:not(.active):hover, .theme-switcher:hover .theme-btn:not(.active):hover {
                color: var(--primary);
                background: var(--bg-card);
            }
        }

        .header-user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-left: 2rem;
            border-left: 1px solid var(--border);
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .header-user-profile:hover {
            opacity: 0.8;
            transform: translateY(-1px);
        }
        
        .header-user-profile:hover .header-user-avatar {
            box-shadow: 0 4px 12px var(--primary-soft);
        }

        .header-user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* text left aligned since it is now on right */
            line-height: 1.2;
        }

        .header-user-name {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
        }

        .header-user-position {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .header-user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .header-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .top-header {
                padding: 1rem;
            }
            .header-user-info {
                display: none;
            }
            
            /* Responsive Welcome Modal */
            .welcome-card {
                flex-direction: column;
                width: 90vw;
            }
            .welcome-left {
                width: 100%;
                height: 100px;
            }
            .waving-hand {
                font-size: 4rem;
            }
            .welcome-right {
                padding: 2rem 1.5rem;
                text-align: center;
                align-items: center;
            }
            .welcome-user-container {
                flex-direction: column;
                gap: 1rem;
            }
            .welcome-avatar-circle {
                width: 100px;
                height: 100px;
                font-size: 2.5rem;
                margin-top: -60px; /* Pull into banner */
            }
            .welcome-user {
                font-size: 1.5rem;
                max-width: 100%;
                white-space: normal; /* allow wrap on small screen */
            }
            .welcome-loader-box {
                justify-content: center;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div id="app-container" class="app-container {{ Route::is('admin.users.index') ? 'account-mgmt-page' : '' }}">
        <!-- Mobile menu button -->
        <button id="mobile-menu-btn" class="mobile-menu-btn">
            <i data-lucide="menu"></i>
        </button>

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar">. 
            <div class="sidebar-content">
                <!-- Header -->
                <div class="sidebar-header">
                    <div class="sidebar-logo">
                        <div class="logo-icon">
                            <i data-lucide="building-2"></i>
                        </div>
                        <div>
                            <h1 class="logo-title">Employee Portal</h1>
                            <p class="logo-subtitle">Management System</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="sidebar-nav">
                    <ul>
                        <li>
                            <a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}">
                                <i data-lucide="layout-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link {{ Route::is('employees.masterlist') || Route::is('employees.add') || (Route::is('employees.show') && (!isset($isArchived) || !$isArchived)) ? 'active' : '' }}" onclick="toggleSubnav(event)">
                                <i data-lucide="users"></i>
                                <span>201 Masterlist</span>
                                <i data-lucide="chevron-down" class="subnav-arrow {{ Route::is('employees.masterlist') || Route::is('employees.add') || (Route::is('employees.show') && (!isset($isArchived) || !$isArchived)) ? 'rotate' : '' }}" style="margin-left: auto; width: 14px; height: 14px; transition: transform 0.3s;"></i>
                            </a>
                            <ul class="subnav" id="masterlistSubnav" style="{{ Route::is('employees.masterlist') || Route::is('employees.add') || (Route::is('employees.show') && (!isset($isArchived) || !$isArchived)) ? 'display: block;' : 'display: none;' }}">
                                <li>
                                    <a href="{{ route('employees.masterlist') }}" class="subnav-link {{ Route::is('employees.masterlist') || (Route::is('employees.show') && (!isset($isArchived) || !$isArchived)) ? 'active' : '' }}">
                                        <i data-lucide="chevron-right"></i>
                                        Masterlist
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('employees.add') }}" class="subnav-link {{ Route::is('employees.add') ? 'active' : '' }}">
                                        <i data-lucide="chevron-right"></i>
                                        Add Employee
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="{{ route('employees.archive') }}" class="nav-link {{ Route::is('employees.archive') || (isset($isArchived) && $isArchived) ? 'active' : '' }}">
                                <i data-lucide="archive"></i>
                                <span>Archive</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('employees.requests') }}" class="nav-link {{ str_contains(Route::currentRouteName(), 'requests') || str_contains(Route::currentRouteName(), 'approved') ? 'active' : '' }}">
                                <i data-lucide="file-text"></i>
                                <span>Request List</span>
                            </a>
                            @if(str_contains(Route::currentRouteName(), 'requests') || str_contains(Route::currentRouteName(), 'approved'))
                            <ul class="subnav">
                                <li>
                                    <a href="{{ route('employees.requests') }}" class="subnav-link {{ Route::is('employees.requests') ? 'active' : '' }}">
                                        <i data-lucide="chevron-right"></i>
                                        Pending Requests
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('employees.approved-list') }}" class="subnav-link {{ Route::is('employees.approved-list') ? 'active' : '' }}">
                                        <i data-lucide="chevron-right"></i>
                                        Approved List
                                    </a>
                                </li>
                            </ul>
                            @endif
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="nav-link {{ str_contains(Route::currentRouteName(), 'admin.users') ? 'active' : '' }}">
                                <i data-lucide="shield-alert"></i>
                                <span>Account Management</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Sidebar Footer -->
                <div class="sidebar-footer">
                    <div class="sidebar-user">
                        <a href="{{ route('profile.edit') }}" class="user-info" style="text-decoration: none; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                            <div class="user-avatar-premium" style="overflow: hidden; padding: 0; display: flex; align-items: center; justify-content: center;">
                                @if(session('welcome_avatar'))
                                    <img src="{{ asset(session('welcome_avatar')) }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    {{ strtoupper(substr(session('auth_user_name', 'A'), 0, 1)) }}
                                @endif
                            </div>
                            <div class="user-details">
                                <span class="user-name">{{ session('auth_user_name', 'Administrator') }}</span>
                                <span class="user-role">{{ session('auth_user_role', 'Super Admin') }}</span>
                            </div>
                        </a>
                        <a href="{{ route('logout') }}" class="btn-logout" title="Logout">
                            <i data-lucide="log-out" style="width: 18px; height: 18px;"></i>
                        </a>
                    </div>
                    <div class="sidebar-footer-content">
                        <p class="footer-text">© 2026 Admin Portal</p>
                        <span class="version-tag">v2.1</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="sidebar-overlay"></div>

        <!-- Main content -->
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <!-- Left side empty to push items to right -->
                </div>
                <div class="header-right">
                    <div class="theme-switcher" id="theme-switcher-container">
                        <button class="theme-btn active" data-theme-btn="light" onclick="setTheme('light', event)" title="Light Mode">
                            <i data-lucide="sun"></i>
                        </button>
                        <button class="theme-btn" data-theme-btn="dark" onclick="setTheme('dark', event)" title="Dark Mode">
                            <i data-lucide="moon"></i>
                        </button>
                        <button class="theme-btn" data-theme-btn="night" onclick="setTheme('night', event)" title="Night Mode (Eye Protection)">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>

                    <!-- User Profile -->
                    <a href="{{ route('profile.edit') }}" class="header-user-profile">
                        <div class="header-user-avatar">
                            @if(session('welcome_avatar'))
                                <img src="{{ asset(session('welcome_avatar')) }}" alt="Profile">
                            @else
                                {{ strtoupper(substr(session('auth_user_name', 'A'), 0, 1)) }}
                            @endif
                        </div>
                        <div class="header-user-info">
                            <span class="header-user-name">{{ session('auth_user_name', 'Administrator') }}</span>
                            <span class="header-user-position">{{ session('auth_user_role', 'Super Admin') }}</span>
                        </div>
                    </a>
                </div>
            </header>

            @yield('content')
        </main>
    </div>

    <!-- Modern Welcome Modal (Wider Rectangle Layout) -->
    @if(session('show_welcome_modal'))
    <div id="welcome-modal-overlay" class="welcome-modal-overlay active">
        <div class="welcome-card">
            <!-- Left Side: Vibrant Waving Section -->
            <div class="welcome-left">
                <div class="waving-hand">👋</div>
            </div>
            
            <!-- Right Side: Clean User Info Section -->
            <div class="welcome-right">
                <div class="welcome-user-container">
                    <div class="welcome-avatar-circle">
                        @if(session('welcome_avatar'))
                            <img src="{{ asset(session('welcome_avatar')) }}" alt="Profile">
                        @else
                            {{ strtoupper(substr(session('welcome_name', 'U'), 0, 1)) }}
                        @endif
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <h2 class="welcome-title">Welcome back,</h2>
                        <h1 class="welcome-user" title="{{ session('welcome_name') }}">{{ session('welcome_name') }}</h1>
                        
                        <div class="welcome-loader-box" style="margin-top: 1rem; display: flex; align-items: center; gap: 10px; color: #4f46e5; font-size: 14px; font-weight: 600;">
                            <i data-lucide="loader" class="animate-spin" style="width: 18px; height: 18px;"></i>
                            <span>Accessing Secure Records...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="welcome-decoration">
                <div class="deco-circle circle-1"></div>
            </div>
        </div>
    </div>
    @endif

    @push('styles')
    <style>
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
    @endpush

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
        }

        // Close sidebar when clicking nav link on mobile
        const navLinks = document.querySelectorAll('.nav-link, .subnav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');
                }
            });
        });

        // Theme Management
        function setTheme(theme, event) {
            if (event) {
                event.stopPropagation();
            }
            
            const switcher = document.getElementById('theme-switcher-container');
            
            // For touch devices, it requires two taps: one to open, one to select.
            // On touch devices, 'hover' doesn't exist natively, we just rely on click adding the 'open' class
            if (event && event.type === 'click') {
                if (window.innerWidth <= 1024 || window.matchMedia('(hover: none)').matches) {
                    if (switcher && !switcher.classList.contains('open')) {
                        switcher.classList.add('open');
                        return; // Stop here, just open it
                    }
                }
            }

            // Update body attribute
            document.body.setAttribute('data-theme', theme);
            
            // Save to local storage
            localStorage.setItem('app-theme', theme);
            
            // Update buttons
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

        // Close theme switcher when clicking outside
        document.addEventListener('click', function(event) {
            const switcher = document.getElementById('theme-switcher-container');
            if (switcher && switcher.classList.contains('open') && !switcher.contains(event.target)) {
                switcher.classList.remove('open');
            }
        });

        // Initialize Theme from Storage or Default to Light
        const savedTheme = localStorage.getItem('app-theme') || 'light';
        // Pass false or null for event so it sets immediately on load without requiring click
        setTheme(savedTheme, null);

        // Welcome Modal Functions
        function closeWelcomeModal() {
            const modal = document.getElementById('welcome-modal-overlay');
            const app = document.getElementById('app-container');
            
            modal.style.opacity = '0';
            modal.style.visibility = 'hidden';
            document.body.classList.remove('modal-open');
            
            setTimeout(() => {
                modal.remove();
            }, 500);
        }

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

        // Apply blur on load and set auto-close if modal exists
        window.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('welcome-modal-overlay')) {
                document.body.classList.add('modal-open');
                
                // Auto close after 2 seconds
                setTimeout(() => {
                    closeWelcomeModal();
                }, 2000);
            }

            // Auto-dismiss success toast if it exists
            const toast = document.getElementById('successToast');
            if (toast) {
                setTimeout(() => {
                    closeToast();
                }, 4000);
            }
            
            // Re-initialize icons just in case
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Global Formatting Script
        document.addEventListener('input', function(e) {
            // 1. Title Case Formatting (Big First Letter, Small Following)
            // Target text inputs and textareas, but skip specific non-title fields
            const isText = (e.target.tagName === 'INPUT' && (e.target.type === 'text' || !e.target.type)) || e.target.tagName === 'TEXTAREA';
            const skipFields = ['email', 'password', 'id', 'username', 'so_no', 'so_number'];
            
            if (isText && !skipFields.includes(e.target.name) && !skipFields.includes(e.target.id)) {
                const cursorStart = e.target.selectionStart;
                const cursorEnd = e.target.selectionEnd;
                
                let val = e.target.value;
                if (val) {
                    // Capitalize first letter of each word and lowercase the rest
                    e.target.value = val.replace(/\w\S*/g, function(txt) {
                        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                    });
                }
                
                // Restore cursor position
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
    </script>
    @stack('scripts')
</body>
</html>
