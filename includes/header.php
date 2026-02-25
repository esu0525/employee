<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <link rel="stylesheet" href="assets/styles.css">
    <!-- Lucide Icons via CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
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
                            <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                                <i data-lucide="users"></i>
                                <span>Master List</span>
                            </a>
                        </li>
                        <li>
                            <a href="history.php" class="nav-link <?php echo (strpos($current_page, 'history') !== false) ? 'active' : ''; ?>">
                                <i data-lucide="history"></i>
                                <span>History</span>
                            </a>
                            <?php if (strpos($current_page, 'history') !== false): ?>
                            <ul class="subnav">
                                <li>
                                    <a href="history-inactive.php" class="subnav-link <?php echo ($current_page == 'history-inactive.php') ? 'active' : ''; ?>">
                                        <i data-lucide="chevron-right"></i>
                                        Inactive
                                    </a>
                                </li>
                                <li>
                                    <a href="history-resign.php" class="subnav-link <?php echo ($current_page == 'history-resign.php') ? 'active' : ''; ?>">
                                        <i data-lucide="chevron-right"></i>
                                        Resign
                                    </a>
                                </li>
                                <li>
                                    <a href="history-retired.php" class="subnav-link <?php echo ($current_page == 'history-retired.php') ? 'active' : ''; ?>">
                                        <i data-lucide="chevron-right"></i>
                                        Retired
                                    </a>
                                </li>
                                <li>
                                    <a href="history-transfer.php" class="subnav-link <?php echo ($current_page == 'history-transfer.php') ? 'active' : ''; ?>">
                                        <i data-lucide="chevron-right"></i>
                                        Transfer
                                    </a>
                                </li>
                            </ul>
                            <?php endif; ?>
                        </li>
                        <li>
                            <a href="requests.php" class="nav-link <?php echo ($current_page == 'requests.php') ? 'active' : ''; ?>">
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
