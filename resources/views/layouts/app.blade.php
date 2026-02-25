<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System - @yield('title')</title>
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
        <aside id="sidebar" class="sidebar">
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
                            <a href="{{ route('employees.index') }}" class="nav-link {{ Route::is('employees.index') ? 'active' : '' }}">
                                <i data-lucide="users"></i>
                                <span>Master List</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('employees.history') }}" class="nav-link {{ str_contains(Route::currentRouteName(), 'history') ? 'active' : '' }}">
                                <i data-lucide="history"></i>
                                <span>History</span>
                            </a>
                            @if(str_contains(Route::currentRouteName(), 'history'))
                            <ul class="subnav">
                                <li>
                                    <a href="{{ route('employees.history-inactive') }}" class="subnav-link {{ Route::is('employees.history-inactive') ? 'active' : '' }}">
                                        <i data-lucide="chevron-right"></i>
                                        Inactive
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('employees.history-resign') }}" class="subnav-link {{ Route::is('employees.history-resign') ? 'active' : '' }}">
                                        <i data-lucide="chevron-right"></i>
                                        Resign
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('employees.history-retired') }}" class="subnav-link {{ Route::is('employees.history-retired') ? 'active' : '' }}">
                                        <i data-lucide="chevron-right"></i>
                                        Retired
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('employees.history-transfer') }}" class="subnav-link {{ Route::is('employees.history-transfer') ? 'active' : '' }}">
                                        <i data-lucide="chevron-right"></i>
                                        Transfer
                                    </a>
                                </li>
                            </ul>
                            @endif
                        </li>
                        <li>
                            <a href="{{ route('employees.requests') }}" class="nav-link {{ Route::is('employees.requests') ? 'active' : '' }}">
                                <i data-lucide="file-text"></i>
                                <span>Request List</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Footer -->
                <div class="sidebar-footer">
                    <div class="sidebar-footer-content">
                        <p class="footer-text">© 2026 Employee Portal</p>
                        <p class="footer-version">Version 2.0</p>
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
