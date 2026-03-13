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
                            <a href="{{ route('employees.index') }}" class="nav-link {{ Route::is('employees.index') || Route::is('employees.masterlist') || request()->is('employee-details*') ? 'active' : '' }}">
                                <i data-lucide="users"></i>
                                <span>Master List</span>
                            </a>
                            @if(Route::is('employees.index') || Route::is('employees.masterlist') || request()->is('employee-details*'))
                            <ul class="subnav">
                                <li>
                                    <a href="{{ route('employees.index') }}" class="subnav-link {{ Route::is('employees.index') ? 'active' : '' }}">
                                        <i data-lucide="chevron-right"></i>
                                        Current Page
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('employees.masterlist') }}" class="subnav-link {{ Route::is('employees.masterlist') ? 'active' : '' }}">
                                        <i data-lucide="chevron-right"></i>
                                        Master List
                                    </a>
                                </li>
                            </ul>
                            @endif
                        </li>
                        <li>
                            <a href="{{ route('employees.history') }}" class="nav-link {{ Route::is('employees.history') ? 'active' : '' }}">
                                <i data-lucide="history"></i>
                                <span>History</span>
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
                        <div class="user-info">
                            <div class="user-avatar-premium">AD</div>
                            <div class="user-details">
                                <span class="user-name">Administrator</span>
                                <span class="user-role">Super Admin</span>
                            </div>
                        </div>
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
                    <div>
                        <h2 class="welcome-title">Welcome back,</h2>
                        <h1 class="welcome-user" title="{{ session('welcome_name') }}">{{ session('welcome_name') }}</h1>
                        
                        <div style="margin-top: 1rem; display: flex; align-items: center; gap: 10px; color: #4f46e5; font-size: 14px; font-weight: 600;">
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

        // Apply blur on load and set auto-close if modal exists
        window.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('welcome-modal-overlay')) {
                document.body.classList.add('modal-open');
                
                // Auto close after 3.8 seconds
                setTimeout(() => {
                    closeWelcomeModal();
                }, 3800);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
