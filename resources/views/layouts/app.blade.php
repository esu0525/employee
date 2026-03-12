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
    @stack('styles')
</head>
<body>
    <div class="app-container">
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
    </script>
    @stack('scripts')
</body>
</html>
