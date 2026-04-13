<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>201 System - @yield('title')</title>
    <link rel="icon" href="{{ asset('assets/images/HRNTP-logo.png') }}" type="image/png">
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
            border: 3px solid white;
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
            border-radius: 50%;
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
        .sidebar-header-container {
            padding: 2.5rem 1.5rem 2rem;
            position: relative;
            transition: var(--transition);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: var(--transition);
        }

        .logo-circle {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            overflow: hidden;
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.3));
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            padding: 2px;
            flex-shrink: 0;
            transition: var(--transition);
        }

        .logo-circle img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .logo-text-wrapper {
            text-align: left;
            flex: 1;
            min-width: 0;
            opacity: 1;
            transform: translateX(0);
            transition: opacity 0.3s ease, transform 0.3s ease, width 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.02em;
            color: white;
            margin: 0;
            line-height: 1.1;
            display: block;
        }

        .logo-subtitle {
            font-size: 10px;
            color: rgba(255,255,255,0.7);
            margin-top: 4px;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (min-width: 1024px) {
            .collapsed-sidebar .sidebar:not(:hover) .logo-text-wrapper {
                opacity: 0;
                transform: translateX(-10px);
                pointer-events: none;
                width: 0;
                display: none !important;
            }
            .collapsed-sidebar .sidebar:not(:hover) .sidebar-header-container {
                padding: 1.5rem 0.5rem;
                display: flex;
                justify-content: center;
            }
            .collapsed-sidebar .sidebar:not(:hover) .sidebar-logo {
                justify-content: center;
                gap: 0;
            }
            .collapsed-sidebar .sidebar:not(:hover) .logo-circle {
                width: 48px;
                height: 48px;
                transform: scale(0.85);
            }
        }

        /* Mobile Close Button Style */
        .mobile-close-btn {
            display: none;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            position: absolute;
            top: 2.25rem;
            right: 1.5rem;
            z-index: 100;
        }

        .mobile-close-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        @media (max-width: 1023px) {
            .mobile-close-btn {
                display: flex;
            }
        }
    </style>
    @stack('styles')
    <style>
        /* Modern Confirmation Modal */
        .confirm-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(8px);
            z-index: 11000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
        }
        .confirm-modal-overlay.active {
            display: flex;
            opacity: 1;
        }
        .confirm-modal-card {
            background: var(--bg-card, #ffffff);
            width: 320px;
            padding: 2.5rem 2rem;
            border-radius: 32px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
            transform: translateY(20px) scale(0.9);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid var(--border, #f1f5f9);
        }
        .confirm-modal-overlay.active .confirm-modal-card {
            transform: translateY(0) scale(1);
        }
        .confirm-modal-icon {
            width: 64px;
            height: 64px;
            background: #fef2f2;
            color: #ef4444;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            animation: pulse-red 2s infinite;
        }
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        .confirm-modal-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: var(--text-main, #1e293b);
        }
        .confirm-modal-card p {
            font-size: 0.9rem;
            color: var(--text-muted, #64748b);
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .confirm-modal-actions {
            display: flex;
            gap: 1rem;
        }
        .btn-confirm-cancel, .btn-confirm-proceed {
            flex: 1;
            padding: 0.9rem;
            border-radius: 16px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .btn-confirm-cancel { 
            background: var(--bg-main, #f1f5f9); 
            color: var(--text-muted, #64748b); 
        }
        .btn-confirm-cancel:hover { background: #e2e8f0; color: #475569; transform: translateY(-1px); }
        .btn-confirm-proceed { 
            background: #ef4444; 
            color: white; 
            box-shadow: 0 8px 20px -6px rgba(239, 68, 68, 0.5);
        }
        .btn-confirm-proceed:hover { 
            background: #dc2626; 
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -6px rgba(239, 68, 68, 0.6);
        }

        body[data-theme="dark"] .confirm-modal-card { background: #1e293b; border-color: #334155; }
        body[data-theme="dark"] .confirm-modal-card h3 { color: #f8fafc; }
        body[data-theme="dark"] .btn-confirm-cancel { background: #334155; color: #94a3b8; }
    </style>
    @stack('styles')
</head>
<body>
    <div id="app-container" class="app-container">
        <script src="{{ asset('assets/js/fouc.js') }}"></script>

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar">
            <div class="sidebar-content">
                @php
                    $currentUser = \App\Models\User::find(session('auth_user_id'));
                    $isAdmin = $currentUser && $currentUser->role === 'admin';
                    
                    $canViewMasterlist = $currentUser && $currentUser->hasPermission('view_masterlist');
                    $canAddEmployee    = $currentUser && $currentUser->hasPermission('edit_masterlist');
                    $canManageRequests = $currentUser && $currentUser->hasPermission('view_requests');
                    $canManageAccounts = $currentUser && $currentUser->hasPermission('manage_accounts');
                    $canViewArchive    = $currentUser && $currentUser->hasPermission('view_archive');
                @endphp
                <div class="sidebar-header sidebar-header-container">
                    <!-- Mobile Close Button -->
                    <button class="mobile-close-btn" onclick="document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebar-overlay').classList.remove('active');">
                        <i data-lucide="x"></i>
                    </button>
                    <div class="sidebar-logo">
                        <div class="logo-circle">
                            <img src="{{ asset('images/logos/HRNTP-logo.jpg') }}" alt="HRNTP">
                        </div>
                        <div class="logo-text-wrapper">
                            <h1 class="logo-title">201 System</h1>
                            <p class="logo-subtitle">
                                Personnel Information & Records <br>Management System
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="sidebar-nav">
                    <ul>
                        <li style="padding: 0 1.5rem; margin-bottom: 0.05rem; margin-left: -20px;">
                            <span style="font-size: 1rem; font-weight: 800; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.15em;">Main</span>
                        </li>
                        <li>
                            <a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}">
                                <i data-lucide="layout-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        @if($isAdmin)
                        <li>
                            <a href="{{ route('admin.audit-trail') }}" class="nav-link {{ Route::is('admin.audit-trail') ? 'active' : '' }}">
                                <i data-lucide="clipboard-list"></i>
                                <span>Audit Trail</span>
                            </a>
                        </li>
                        @endif
                        @php $hasOperHeader = false; @endphp
                        @if($canViewMasterlist || $canAddEmployee)
                        <li style="padding: 0 1.5rem; margin: 1.5rem 0 0.05rem; margin-left: -20px;">
                            <span style="font-size: 1rem; font-weight: 800; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.15em;">Operations</span>
                        </li>
                        @php $hasOperHeader = true; @endphp
                        <li>
                            <a href="#" class="nav-link {{ Route::is('employees.masterlist') || Route::is('employees.add') || (Route::is('employees.show') && (!isset($isArchived) || !$isArchived)) ? 'active' : '' }}" onclick="toggleSubnav(event)">
                                <i data-lucide="users"></i>
                                <span>201 Masterlist</span>
                                <i data-lucide="chevron-down" class="subnav-arrow {{ Route::is('employees.masterlist') || Route::is('employees.add') || (Route::is('employees.show') && (!isset($isArchived) || !$isArchived)) ? 'rotate' : '' }}" style="margin-left: auto; width: 14px; height: 14px; transition: transform 0.3s;"></i>
                            </a>
                            <ul class="subnav" id="masterlistSubnav" style="{{ Route::is('employees.masterlist') || Route::is('employees.add') || (Route::is('employees.show') && (!isset($isArchived) || !$isArchived)) ? 'display: block;' : 'display: none;' }}">
                                @if($canViewMasterlist)
                                <li>
                                    <a href="{{ route('employees.masterlist') }}" class="subnav-link {{ Route::is('employees.masterlist') || (Route::is('employees.show') && (!isset($isArchived) || !$isArchived)) ? 'active' : '' }}">
                                        <i data-lucide="chevron-right"></i>
                                        Masterlist
                                    </a>
                                </li>
                                @endif
                                @if($canAddEmployee)
                                <li>
                                    <a href="{{ route('employees.add') }}" class="subnav-link {{ Route::is('employees.add') ? 'active' : '' }}">
                                        <i data-lucide="chevron-right"></i>
                                        Add Employee
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if($canViewArchive)
                        <li>
                            <a href="{{ route('employees.archive') }}" class="nav-link {{ Route::is('employees.archive') || (isset($isArchived) && $isArchived) ? 'active' : '' }}" onclick="const lastUrl = localStorage.getItem('archiveLastUrl'); if(lastUrl && lastUrl.includes('/archive') && !window.location.href.includes('/archive')) { event.preventDefault(); window.location.href = lastUrl; }">
                                <i data-lucide="archive"></i>
                                <span>Archive</span>
                            </a>
                        </li>
                        @endif
                        @if($canManageRequests)
                        <li>
                            <a href="{{ route('employees.requests') }}" class="nav-link {{ Route::is('employees.requests') || Route::is('employees.approved-list') ? 'active' : '' }}">
                                <i data-lucide="file-stack"></i>
                                <span>Request Center</span>
                            </a>
                        </li>
                        @endif
                        @if($canManageAccounts)
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="nav-link {{ str_contains(Route::currentRouteName(), 'admin.users') ? 'active' : '' }}">
                                <i data-lucide="shield-alert"></i>
                                <span>Account Management</span>
                            </a>
                        </li>
                        @endif

                        {{-- My Profile for Staff Only --}}
                        @if(!$isAdmin)
                        <li>
                            <a href="{{ route('profile.edit') }}" class="nav-link {{ Route::is('profile.edit') ? 'active' : '' }}">
                                <i data-lucide="user-circle"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </nav>

                <!-- Sidebar Footer -->
                <div class="sidebar-footer">
                    <div class="sidebar-user">
                        <a href="{{ route('logout') }}" class="user-info" style="cursor: pointer; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; min-width: 0; flex: 1;" title="Logout">
                            <div class="user-avatar-premium" style="overflow: hidden; padding: 0; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); flex-shrink: 0;">
                                @if($currentUser && $currentUser->profile_picture && file_exists(public_path($currentUser->profile_picture)))
                                    <img src="{{ asset($currentUser->profile_picture) }}?v={{ $currentUser->updated_at->timestamp ?? time() }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @elseif(session('welcome_avatar'))
                                    <img src="{{ asset(session('welcome_avatar')) }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @else
                                    <span style="font-weight: 700; color: white;">{{ strtoupper(substr(session('auth_user_name', 'A'), 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="user-details">
                                <span class="user-name" style="color: white !important;">{{ session('auth_user_name', 'Administrator') }}</span>
                                <span class="user-role">{{ ucfirst(session('auth_user_role', 'Admin')) }}</span>
                            </div>
                        </a>
                        <a href="{{ route('logout') }}" class="btn-logout" title="Logout" style="color: rgba(255,255,255,0.6); transition: all 0.2s;">
                            <i data-lucide="log-out" style="width: 20px; height: 20px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="sidebar-overlay"></div>

    <!-- Modern Confirmation Modal -->
    <div id="confirmModal" class="confirm-modal-overlay">
        <div class="confirm-modal-card">
            <div id="confirmIconBox" class="confirm-modal-icon">
                <i data-lucide="help-circle" style="width: 32px; height: 32px;"></i>
            </div>
            <h3 id="confirmModalTitle">Are you sure?</h3>
            <p id="confirmModalMessage">This action cannot be undone.</p>
            <div class="confirm-modal-actions">
                <button type="button" class="btn-confirm-cancel" id="confirmModalCancel">Cancel</button>
                <button type="button" class="btn-confirm-proceed" id="confirmModalProceed">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <main class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button id="mobile-menu-btn" class="mobile-menu-btn" style="display: flex; position: static; background: transparent; border: none; padding: 0.5rem; color: var(--text-main); cursor: pointer; transition: 0.2s;">
                        <i data-lucide="menu" style="width: 24px; height: 24px;"></i>
                    </button>
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
                    <a href="{{ route('logout') }}" class="header-user-profile" title="Logout">
                        <div class="header-user-avatar">
                            @if($currentUser && $currentUser->profile_picture && file_exists(public_path($currentUser->profile_picture)))
                                <img src="{{ asset($currentUser->profile_picture) }}?v={{ $currentUser->updated_at->timestamp ?? time() }}" alt="Profile" style="border-radius: 50%; object-fit: cover;">
                            @elseif(session('welcome_avatar'))
                                <img src="{{ asset(session('welcome_avatar')) }}" alt="Profile" style="border-radius: 50%; object-fit: cover;">
                            @else
                                {{ strtoupper(substr(session('auth_user_name', 'A'), 0, 1)) }}
                            @endif
                        </div>
                        <div class="header-user-info">
                            <span class="header-user-name">{{ session('auth_user_name', 'Administrator') }}</span>
                            <span class="header-user-position">{{ ucfirst(session('auth_user_role', 'admin')) }}</span>
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
                        @if($currentUser && $currentUser->profile_picture && file_exists(public_path($currentUser->profile_picture)))
                            <img src="{{ asset($currentUser->profile_picture) }}?v={{ $currentUser->updated_at->timestamp ?? time() }}" alt="Profile">
                        @elseif(session('welcome_avatar'))
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

    <script src="{{ asset('assets/js/app.js') }}"></script>

    {{-- Session Heartbeat for "Keep Me Logged In" --}}
    <meta name="keep-logged-in" content="{{ session('keep_logged_in') ? '1' : '0' }}">
    <meta name="heartbeat-url" content="{{ route('session.heartbeat') }}">
    <script>
    (function() {
        const heartbeatUrl = document.querySelector('meta[name="heartbeat-url"]')?.content;
        const isAuthenticated = {{ session()->has('auth_user_id') ? 'true' : 'false' }};

        if (isAuthenticated && heartbeatUrl) {
            // Ping the server every 5 minutes to keep the session alive
            const HEARTBEAT_INTERVAL = 5 * 60 * 1000; // 5 minutes

            function sendHeartbeat() {
                fetch(heartbeatUrl, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.status === 401) {
                        console.error('[Session] Heartbeat failed (401). Redirecting...');
                        window.location.href = '/login';
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.alive) {
                        const mode = data.keep_logged_in ? 'Persistent' : 'Standard';
                        console.log(`[Session] Heartbeat OK (${mode}) — auto-sync at ${new Date(Date.now() + HEARTBEAT_INTERVAL).toLocaleTimeString()}`);
                    }
                })
                .catch(err => {
                    console.warn('[Session] Network glitch during heartbeat. System will retry.');
                });
            }

            // Start heartbeat interval
            setInterval(sendHeartbeat, HEARTBEAT_INTERVAL);

            // Give the browser 10 seconds to stabilize before first sync
            setTimeout(sendHeartbeat, 10000);
        }
    })();
    </script>

    @stack('scripts')
</body>
</html>

