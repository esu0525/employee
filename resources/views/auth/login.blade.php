<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - 201 System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* ─── Modern Inline Alert ─── */
        .inline-alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.25rem;
            background: #fffafa;
            border: 1px solid #fee2e2;
            border-radius: 12px;
            color: #ef4444;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            animation: slideDownIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .inline-alert.success {
            background: #f0fdf4;
            border-color: #dcfce7;
            color: #16a34a;
        }

        @keyframes slideDownIn {
            from {
                opacity: 0;
                transform: translateY(-12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        :root {
            --primary: #6366f1;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            --text-main: #1e293b;
            --text-muted: #64748b;
            --white: #ffffff;
            --input-bg: #f8fafc;
            --transition: all 0.7s cubic-bezier(0.645, 0.045, 0.355, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .auth-wrapper {
            position: relative;
            width: 100%;
            min-height: 100vh;
            display: flex;
            background: white;
            overflow-x: hidden;
        }

        /* ─── Animated Background (Restricted to secondary half) ─── */
        .bg-canvas {
            position: fixed;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            z-index: 0;
            background: #312e81;
            overflow: hidden;
            transition: var(--transition);
        }

        .auth-wrapper.show-otp .bg-canvas,
        .auth-wrapper.show-register .bg-canvas {
            left: 0;
        }

        /* Gradient mesh */
        .bg-canvas::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 30%, rgba(99, 102, 241, 0.6) 0%, transparent 60%),
                radial-gradient(circle at 80% 70%, rgba(139, 92, 246, 0.5) 0%, transparent 60%),
                radial-gradient(circle at 50% 10%, rgba(168, 85, 247, 0.4) 0%, transparent 50%);
            animation: meshShift 10s ease-in-out infinite alternate;
        }

        @keyframes meshShift {
            0% {
                filter: hue-rotate(0deg) brightness(1);
            }

            50% {
                filter: hue-rotate(15deg) brightness(1.1);
            }

            100% {
                filter: hue-rotate(-10deg) brightness(0.9);
            }
        }

        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            opacity: 0;
            animation: particleFly linear infinite;
            pointer-events: none;
        }

        @keyframes particleFly {
            0% {
                opacity: 0;
                transform: translateY(110vh) scale(0);
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.3;
            }

            100% {
                opacity: 0;
                transform: translateY(-10vh) scale(1);
            }
        }

        /* Grid lines */
        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* ─── Form Container (Static Content) ─── */
        .form-container {
            position: absolute;
            top: 0;
            width: 50%;
            height: 100%;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 3rem;
            z-index: 1;
            overflow-y: auto;
            text-align: center;
        }

        .otp-field.error-shake {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.1) !important;
        }

        .login-container {
            left: 0;
            z-index: 2;
            overflow: hidden;
        }

        .register-container {
            left: 100%;
            opacity: 0;
            z-index: 10;
            pointer-events: none;
        }

        .otp-container {
            left: 0;
            opacity: 0;
            z-index: 10;
            pointer-events: none;
        }

        .auth-wrapper.show-register .login-container {
            opacity: 0;
            pointer-events: none;
        }

        .auth-wrapper.show-register .register-container {
            opacity: 1;
            pointer-events: all;
            left: 50%;
        }

        .auth-wrapper.show-otp .login-container {
            opacity: 0;
            pointer-events: none;
        }

        .auth-wrapper.show-otp .otp-container {
            opacity: 1;
            pointer-events: all;
            left: 50%;
        }

        .auth-wrapper.show-otp .branding-panel {
            left: 0;
        }

        .form-inner {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
        }

        .form-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.4rem;
            letter-spacing: -0.03em;
        }

        .form-subtitle {
            font-size: 1rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        /* ─── Alerts ─── */
        .alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            animation: slideDown 0.4s ease;
        }

        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ─── Form Inputs ─── */
        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-label {
            display: block;
            text-align: left;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-main);
            font-size: 0.875rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 3.25rem;
            padding: 0 1rem 0 3.25rem;
            background: var(--input-bg);
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 0.95rem;
            text-align: left;
            transition: all 0.2s;
            outline: none;
            color: var(--text-main);
        }

        .form-input:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.25rem;
        }

        /* ─── Remember & Forgot ─── */
        .form-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        .remember-me input {
            width: 1.1rem;
            height: 1.1rem;
            border-radius: 4px;
            accent-color: var(--primary);
        }

        .forgot-link {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .btn-submit {
            width: 100%;
            height: 3.25rem;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
            transition: all 0.3s;
            margin-bottom: 1rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 35px -5px rgba(99, 102, 241, 0.5);
        }

        .switch-text {
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .switch-link {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            margin-left: 0.25rem;
            cursor: pointer;
        }

        .portal-link-container {
            margin-top: 1.5rem;
            text-align: center;
        }

        .portal-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 600;
            transition: all 0.2s;
        }

        .portal-link:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* ─── Footer Text ─── */
        .form-footer {
            margin-top: 1.5rem;
            text-align: center;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
        }

        .footer-secure {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }

        .footer-links a {
            font-size: 0.8125rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        .footer-links span {
            color: #cbd5e1;
        }

        /* ─── OTP Specific Styling ─── */
        .otp-inputs {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .otp-field {
            width: 3.5rem;
            height: 4rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: var(--input-bg);
            transition: all 0.2s;
            outline: none;
            font-family: 'Outfit', sans-serif;
        }

        .otp-field:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .resend-timer {
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 1.5rem;
        }

        .btn-resend {
            color: var(--primary);
            font-weight: 700;
            background: none;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .btn-resend:disabled {
            color: #cbd5e1;
            cursor: not-allowed;
        }

        /* ─── Lockout Overlay ─── */
        .lockout-overlay {
            display: none;
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            z-index: 100;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            backdrop-filter: blur(10px);
        }

        .lockout-overlay.visible {
            display: flex;
        }

        .lockout-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            color: #ef4444;
            animation: lockBounce 1s ease-in-out infinite alternate;
        }

        @keyframes lockBounce {
            from { transform: translateY(0); }
            to { transform: translateY(-10px); }
        }

        .lockout-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .lockout-desc {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .lockout-timer {
            font-family: 'Outfit', sans-serif;
            font-size: 3rem;
            font-weight: 800;
            color: #ef4444;
            letter-spacing: 0.05em;
            margin-bottom: 2rem;
        }

        .lockout-bar {
            width: 240px;
            height: 6px;
            background: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
        }

        .lockout-progress {
            height: 100%;
            background: #ef4444;
            width: 100%;
            transition: width 1s linear;
        }
        .branding-panel {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            background: transparent;
            z-index: 60;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            overflow: hidden;
            pointer-events: none;
        }

        .branding-content {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 55;
            position: relative;
        }

        .auth-wrapper.show-register .branding-panel {
            left: 0;
        }

        .auth-wrapper.show-otp .branding-panel {
            left: 0;
        }

        .brand-logo {
            width: clamp(100px, 30%, 240px);
            height: auto;
            border-radius: 50%;
            margin-bottom: 2rem;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.3));
            animation: logoFloat 6s ease-in-out infinite;
            object-fit: contain;
        }

        @keyframes logoFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(2.5rem, 5vw, 5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(to bottom, #ffffff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-tagline {
            font-size: clamp(1rem, 2vw, 1.5rem);
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            max-width: 400px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* ─── Moving Blobs & Cubes Inside Branding ─── */
        @keyframes floatBlob {
            from {
                transform: translate(-20%, -20%) rotate(0deg);
            }

            to {
                transform: translate(30%, 30%) rotate(360deg);
            }
        }

        /* ─── Password Strength ─── */
        .password-strength {
            margin-top: 0.5rem;
            height: 4px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s;
        }

        .strength-text {
            font-size: 0.75rem;
            margin-top: 0.25rem;
            font-weight: 600;
            text-align: right;
        }

        /* ─── Tablets ─── */
        @media (max-width: 768px) {
            .branding-panel {
                display: none;
            }

            .form-container,
            .login-container,
            .register-container,
            .otp-container {
                position: relative !important;
                width: 100% !important;
                left: 0 !important;
                padding: 3rem 2rem;
                justify-content: center;
                background: rgba(255, 255, 255, 0.92);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                transition: none !important;
                /* Fixed on mobile, no slide */
                display: none;
                /* Hide all by default in mobile flex stack */
            }

            .auth-wrapper {
                flex-direction: column;
                background: #312e81;
            }

            /* State visibility for mobile */
            .auth-wrapper:not(.show-register):not(.show-otp) .login-container {
                display: flex !important;
                opacity: 1;
                pointer-events: all;
            }

            .auth-wrapper.show-register .register-container {
                display: flex !important;
                opacity: 1;
                pointer-events: all;
            }

            .auth-wrapper.show-otp .otp-container {
                display: flex !important;
                opacity: 1;
                pointer-events: all;
            }

            .form-inner {
                max-width: 480px;
            }

            .form-footer {
                margin-top: 2rem;
            }
        }

        /* ─── Mobile ─── */
        @media (max-width: 600px) {

            .form-container,
            .login-container,
            .register-container,
            .otp-container {
                padding: 2rem 1.25rem !important;
                min-height: 100vh;
            }

            .form-title {
                font-size: 1.75rem;
                margin-bottom: 0.35rem;
            }

            .form-subtitle {
                font-size: 0.95rem;
                margin-bottom: 1.5rem;
            }

            .form-inner {
                width: 100%;
                max-width: 100%;
            }

            .form-input {
                height: 3.25rem;
                font-size: 0.9rem;
                padding-left: 3rem;
            }

            .input-icon {
                left: 1rem;
            }

            .input-icon svg {
                width: 16px;
                height: 16px;
            }

            .btn-submit {
                height: 3.25rem;
                font-size: 0.95rem;
                border-radius: 10px;
            }

            .form-meta {
                flex-direction: row;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.8rem;
                margin-bottom: 1.75rem;
            }

            .remember-me {
                font-size: 0.8rem;
                gap: 0.5rem;
            }

            .forgot-link {
                font-size: 0.8rem;
            }

            .portal-link {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
            }

            /* Responsive OTP Fields */
            .otp-field {
                width: 42px;
                height: 52px;
                font-size: 1.5rem;
            }

            .otp-wrapper {
                gap: 0.4rem;
            }

            .form-footer {
                margin-top: 1.5rem;
                padding-top: 1.25rem;
            }

            .footer-secure {
                font-size: 0.8rem;
            }

            .footer-links a {
                font-size: 0.75rem;
            }
        }

        /* ─── Very small phones ─── */
        @media (max-width: 380px) {

            .form-container,
            .login-container,
            .register-container,
            .otp-container {
                padding: 1.5rem 1rem !important;
            }

            .form-title {
                font-size: 1.5rem;
            }

            .otp-field {
                width: 36px;
                height: 46px;
                font-size: 1.25rem;
            }

            .otp-wrapper {
                gap: 0.25rem;
            }

            .form-subtitle {
                font-size: 0.875rem;
                margin-bottom: 1.25rem;
            }

            .form-input {
                height: 3rem;
                font-size: 0.85rem;
            }

            .btn-submit {
                height: 3rem;
                font-size: 0.875rem;
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-10px);
            }

            40%,
            80% {
                transform: translateX(10px);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>
</head>

<body>

    <div class="auth-wrapper" id="authWrapper">
        <!-- ─── Animated Background ─── -->
        <div class="bg-canvas">
            <div class="bg-grid"></div>
            <div id="particleContainer"></div>
        </div>

        <!-- ─── Login Form ─── -->
        <div class="form-container login-container">
            <div class="form-inner">
                <h1 class="form-title">Welcome back</h1>
                <p class="form-subtitle">Please enter your credentials to access the 201 System</p>

                <div id="alertPlaceholder"></div>

                <form method="POST" action="{{ route('auth.login') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-wrapper">
                            <i data-lucide="mail" class="input-icon"></i>
                            <input type="email" name="email" class="form-input" placeholder="name@agency.gov.ph"
                                required autocomplete="email" autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-wrapper">
                            <i data-lucide="lock" class="input-icon"></i>
                            <input type="password" name="password" id="loginPassword" class="form-input"
                                placeholder="••••••••" required autocomplete="current-password">
                            <button type="button" class="toggle-password"
                                onclick="togglePass('loginPassword', this)">
                                <i data-lucide="eye" id="loginPasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-meta">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span>Sign In</span>
                    </button>
                </form>

                <div class="portal-link-container">
                    <a href="{{ route('landing') }}" class="portal-link">
                        <i data-lucide="external-link" style="width: 18px;"></i>
                        Return to Home
                    </a>
                </div>

                <div class="form-footer">
                    <p class="footer-secure">
                        <i data-lucide="shield-check"
                            style="width: 14px; display: inline; vertical-align: middle; margin-right: 2px;"></i>
                        Authenticated by Schools Division Office of Quezon City ICT Division
                    </p>
                    <div class="footer-links">
                        <a href="{{ route('legal.privacy') }}">Privacy Policy</a>
                        <span>&bull;</span>
                        <a href="{{ route('legal.terms') }}">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Branding Panel (Sliding) ─── -->
        <div class="branding-panel">
            <div class="branding-content">
                <img src="{{ asset('images/logos/HRNTP-logo.jpg') }}" alt="HRNTP Logo" class="brand-logo">
                <h2 class="brand-name">201 System</h2>
                <p class="brand-tagline">Personnel Information & Records Management System</p>
            </div>
        </div>

        <!-- ─── OTP Container (Hidden by default) ─── -->
        <div class="form-container otp-container" style="text-align: center;">
            <div class="form-inner">
                <button onclick="hideOtp()"
                    style="margin: 0 auto 2rem auto; background: none; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                    <i data-lucide="arrow-left" style="width: 18px;"></i> Back to Login
                </button>
                <h1 class="form-title">Verification Required</h1>
                <p class="form-subtitle">We've sent a 6-digit code to your registered email.</p>
                
                <!-- Countdown -->
                <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 2rem;">
                    <div style="text-align: center;">
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">OTP Expires In</div>
                        <div id="otpTimerDisplay" style="font-family: 'JetBrains Mono', monospace; font-size: 1.25rem; font-weight: 800; color: #ef4444;">10:00</div>
                    </div>
                </div>

                <div id="otpAlertPlaceholder"></div>

                <form id="otpForm" action="{{ route('auth.otp.verify') }}" method="POST">
                    @csrf
                    <input type="hidden" name="otp" id="full_otp">
                    <div id="otpBoxWrapper" class="otp-inputs">
                        <input type="text" maxlength="1" class="otp-field" autofocus>
                        <input type="text" maxlength="1" class="otp-field">
                        <input type="text" maxlength="1" class="otp-field">
                        <input type="text" maxlength="1" class="otp-field">
                        <input type="text" maxlength="1" class="otp-field">
                        <input type="text" maxlength="1" class="otp-field">
                    </div>

                    <!-- Failed Attempts -->
                    <div style="text-align: center; margin-bottom: 1.5rem;">
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Failed Attempts</div>
                        <div class="attempts-row" style="display: flex; gap: 6px; justify-content: center;">
                            <div class="attempt-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #e2e8f0; transition: background 0.3s;"></div>
                            <div class="attempt-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #e2e8f0; transition: background 0.3s;"></div>
                            <div class="attempt-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #e2e8f0; transition: background 0.3s;"></div>
                            <div class="attempt-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #e2e8f0; transition: background 0.3s;"></div>
                            <div class="attempt-dot" style="width: 8px; height: 8px; border-radius: 50%; background: #e2e8f0; transition: background 0.3s;"></div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="verifyBtn">
                        Verify & Access Account
                    </button>
                </form>

                <p class="resend-timer">
                    Didn't receive a code?
                    <button id="resendBtn" class="btn-resend" onclick="handleResend()" disabled>Resend Code (<span
                            id="timer">30</span>s)</button>
                </p>

                <!-- Lockout Overlay -->
                <div id="lockoutOverlay" class="lockout-overlay">
                    <div class="lockout-icon">🔒</div>
                    <h2 class="lockout-title">Account Locked</h2>
                    <p class="lockout-desc">Too many failed attempts. Please wait for the security countdown to end.</p>
                    <div id="lockoutTimerDisplay" class="lockout-timer">03:00</div>
                    <div class="lockout-bar">
                        <div id="lockoutProgress" class="lockout-progress"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        window.LOG_RESEND_URL = "{{ route('auth.otp.resend') }}";
        window.LOG_VERIFY_URL = "{{ route('auth.otp.verify') }}";
    </script>
    <script src="{{ asset('assets/js/login.js') }}"></script>
    <script>
        lucide.createIcons();

        @if(session('error'))
            document.addEventListener('DOMContentLoaded', () => showAlert("{{ session('error') }}", 'error'));
        @endif

        @if(session('success'))
            document.addEventListener('DOMContentLoaded', () => showAlert("{{ session('success') }}", 'success'));
        @endif

        // Handle Persistent Lockout from Server
        @if(isset($isLocked) && $isLocked)
            document.addEventListener('DOMContentLoaded', () => {
                showOTP();
                showLockout({{ $lockedSeconds }});
            });
        @endif
    </script>
</body>

</html>