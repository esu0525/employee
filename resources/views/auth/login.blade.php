<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DepEd 201 System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

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

        /* ─── Faded Blue Background Glow (Centered behind form) ─── */
        .bg-blobs {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 1; /* Above basic background */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .blob-1 {
            position: absolute;
            width: 1000px;
            height: 1000px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.18) 0%, rgba(99, 102, 241, 0.08) 40%, rgba(255, 255, 255, 0) 70%);
            filter: blur(50px);
            border-radius: 50%;
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
            padding: 4rem;
            z-index: 1;
            overflow-y: auto;
        }

        .login-container { left: 0; z-index: 2; }
        .register-container { left: 0; opacity: 0; z-index: 1; pointer-events: none; }

        .auth-wrapper.show-register .login-container { opacity: 0; pointer-events: none; left: 50%; }
        .auth-wrapper.show-register .register-container { opacity: 1; pointer-events: all; left: 50%; z-index: 2; }

        .form-inner { width: 100%; max-width: 440px; position: relative; z-index: 10; }

        .form-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.75rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.75rem;
            letter-spacing: -0.03em;
        }

        .form-subtitle {
            font-size: 1.125rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
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
        .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .alert-success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        /* ─── Form Inputs ─── */
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main); font-size: 0.875rem; }
        .input-wrapper { position: relative; }
        .input-icon { position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; }
        
        .form-input {
            width: 100%;
            height: 3.75rem;
            padding: 0 1rem 0 3.5rem;
            background: var(--input-bg);
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.2s;
            outline: none;
            color: var(--text-main);
        }
        .form-input:focus { background: white; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }

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
            margin-bottom: 2.5rem;
            font-size: 0.9375rem;
        }
        .remember-me { display: flex; align-items: center; gap: 0.75rem; color: var(--text-muted); cursor: pointer; }
        .remember-me input { width: 1.1rem; height: 1.1rem; border-radius: 4px; accent-color: var(--primary); }
        .forgot-link { color: var(--primary); font-weight: 600; text-decoration: none; }

        .btn-submit {
            width: 100%;
            height: 4rem;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.125rem;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
            transition: all 0.3s;
            margin-bottom: 1.5rem;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 20px 35px -5px rgba(99, 102, 241, 0.5); }

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

        .portal-link-container { margin-top: 1.5rem; text-align: center; }
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
        .portal-link:hover { border-color: var(--primary); color: var(--primary); }

        /* ─── Footer Text ─── */
        .form-footer {
            margin-top: 3rem;
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #f1f5f9;
        }
        .footer-secure { color: var(--text-muted); font-size: 0.9375rem; margin-bottom: 1rem; }
        .footer-links { display: flex; justify-content: center; gap: 0.5rem; }
        .footer-links a { font-size: 0.8125rem; color: var(--text-muted); text-decoration: none; }
        .footer-links span { color: #cbd5e1; }

        /* ─── Branding Side (Sliding Panel) ─── */
        .branding-panel {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            background: radial-gradient(circle at top right, #4f46e5 0%, #1e1b4b 100%);
            z-index: 10;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            overflow: hidden;
        }

        .auth-wrapper.show-register .branding-panel { left: 0; }

        .branding-content { position: relative; z-index: 5; }
        .brand-logo {
            width: 420px;
            margin-bottom: 2.5rem;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3)) brightness(2) saturate(1.4);
            animation: logoFloat 6s ease-in-out infinite;
            mix-blend-mode: lighten;
        }
        @keyframes logoFloat { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }

        .brand-name { font-family: 'Outfit', sans-serif; font-size: 5rem; font-weight: 800; margin-bottom: 1rem; background: linear-gradient(to bottom, #ffffff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .brand-tagline { font-size: 1.5rem; color: rgba(255, 255, 255, 0.8); font-weight: 500; max-width: 400px; margin: 0 auto; }

        /* ─── Moving Blobs & Cubes Inside Branding ─── */
        .branding-bg { position: absolute; inset: 0; z-index: 1; perspective: 1000px; }
        
        .branding-blob {
            position: absolute;
            width: 500px; height: 500px;
            background: rgba(99, 102, 241, 0.2);
            filter: blur(100px);
            border-radius: 50%;
            animation: floatBlob 20s infinite alternate linear;
            z-index: 1;
        }

        /* ─── Floating Shapes (Circles & Triangles) ─── */
        .shape-container {
            position: absolute;
            inset: 0;
            z-index: 1;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            pointer-events: none;
            opacity: 0.15;
            filter: blur(2px);
            transition: transform 0.3s ease-out;
        }

        .shape-circle {
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0.05) 100%);
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.1), inset 0 0 15px rgba(255, 255, 255, 0.1);
        }

        .shape-triangle {
            width: 0;
            height: 0;
            background: transparent !important;
            border-left: var(--tri-size, 100px) solid transparent;
            border-right: var(--tri-size, 100px) solid transparent;
            border-bottom: calc(var(--tri-size, 100px) * 1.73) solid rgba(255, 255, 255, 0.1);
            filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.2)) blur(5px);
        }

        /* Drift Animations */
        @keyframes drift-1 {
            0% { transform: translate(0, 0) rotate(0deg) scale(1); }
            50% { transform: translate(10vw, 15vh) rotate(180deg) scale(1.2); }
            100% { transform: translate(0, 0) rotate(360deg) scale(1); }
        }

        @keyframes drift-2 {
            0% { transform: translate(20vw, 40vh) rotate(0deg) scale(1.1); }
            50% { transform: translate(-5vw, -10vh) rotate(-120deg) scale(0.8); }
            100% { transform: translate(20vw, 40vh) rotate(-240deg) scale(1.1); }
        }

        @keyframes drift-3 {
            0% { transform: translate(30vw, -10vh) rotate(0deg); }
            50% { transform: translate(-10vw, 60vh) rotate(90deg) scale(1.5); }
            100% { transform: translate(30vw, -10vh) rotate(180deg); }
        }

        /* Variable Sizes */
        .size-lg { --tri-size: 120px; width: 200px; height: 200px; }
        .size-md { --tri-size: 70px; width: 120px; height: 120px; }
        .size-sm { --tri-size: 40px; width: 60px; height: 60px; }
        .size-xl { --tri-size: 180px; width: 320px; height: 320px; opacity: 0.1; }

        .shape:nth-child(1) { top: 10%; left: 10%; animation: drift-1 40s infinite linear; }
        .shape:nth-child(2) { top: 60%; left: 40%; animation: drift-2 50s infinite linear; }
        .shape:nth-child(3) { top: 20%; left: 75%; animation: drift-3 60s infinite linear; }
        .shape:nth-child(4) { top: 80%; left: 15%; animation: drift-1 45s infinite reverse linear; }
        .shape:nth-child(5) { top: 0%; left: 60%; animation: drift-2 55s infinite linear; }
        .shape:nth-child(6) { top: 40%; left: 20%; animation: drift-3 70s infinite reverse linear; }
        .shape:nth-child(7) { top: 70%; left: 80%; animation: drift-1 35s infinite linear; }
        .shape:nth-child(8) { top: 15%; left: 45%; animation: drift-2 48s infinite reverse linear; }
        .shape:nth-child(9) { top: 85%; left: 55%; animation: drift-3 65s infinite linear; }
        .shape:nth-child(10) { top: 30%; left: 90%; animation: drift-1 52s infinite linear; }

        @keyframes floatBlob { from { transform: translate(-20%, -20%) rotate(0deg); } to { transform: translate(30%, 30%) rotate(360deg); } }

        /* ─── Password Strength ─── */
        .password-strength { margin-top: 0.5rem; height: 4px; background: #e2e8f0; border-radius: 4px; overflow: hidden; }
        .strength-bar { height: 100%; width: 0%; transition: all 0.3s; }
        .strength-text { font-size: 0.75rem; margin-top: 0.25rem; font-weight: 600; text-align: right; }

        @media (max-width: 1024px) {
            .branding-panel { display: none; }
            .form-container { width: 100%; padding: 2rem; justify-content: flex-start; padding-top: 5rem; }
            .auth-wrapper.show-register .register-container { left: 0; }
        }

        @media (max-width: 480px) {
            .form-container { padding: 1.5rem; padding-top: 3rem; }
            .form-title { font-size: 2rem; margin-bottom: 0.5rem; }
            .form-subtitle { font-size: 1rem; margin-bottom: 2rem; }
            .form-inner { width: 100%; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="auth-wrapper" id="authWrapper">
    
    <!-- ─── Login Form ─── -->
    <div class="form-container login-container">
        <div class="bg-blobs"><div class="blob-1"></div></div>
        <div class="form-inner">
            <h1 class="form-title">Welcome back</h1>
            <p class="form-subtitle">Please enter your credentials to access the 201 System</p>

            @if(session('error'))
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Work Email</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></span>
                        <input type="email" name="email" class="form-input" placeholder="admin@deped.gov.ph" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                        <input type="password" name="password" id="login-pwd" class="form-input" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePass('login-pwd', this)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="form-meta">
                    <label class="remember-me">
                        <input type="checkbox">
                        Keep me logged in
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-submit">Sign In to System</button>

                <p class="switch-text">
                    Don't have an account yet? <a class="switch-link" onclick="toRegister()">Sign Up</a>
                </p>

                <div class="portal-link-container">
                    <span style="font-size: 0.875rem; color: var(--text-muted); display: block; margin-bottom: 0.75rem;">Not an administrator?</span>
                    <a href="{{ route('portal.index') }}" class="portal-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Go to Request Form
                    </a>
                </div>
            </form>

            <div class="form-footer">
                <p class="footer-secure">Authentication secured by DepEd IT Division</p>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <span>•</span>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── Register Form ─── -->
    <div class="form-container register-container">
        <div class="bg-blobs"><div class="blob-1"></div></div>
        <div class="form-inner">
            <h1 class="form-title">Create Account</h1>
            <p class="form-subtitle">Register to manage your school personnel records</p>

            @if($errors->any())
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('auth.register') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                        <input type="text" name="name" class="form-input" placeholder="Juan Dela Cruz" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Work Email</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></span>
                        <input type="email" name="email" class="form-input" placeholder="juan@deped.gov.ph" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                        <input type="password" name="password" id="reg-pwd" class="form-input" placeholder="••••••••" required oninput="checkStrength(this.value)">
                        <button type="button" class="toggle-password" onclick="togglePass('reg-pwd', this)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <p class="strength-text" id="strengthText"></p>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                        <input type="password" name="password_confirmation" id="reg-confirm" class="form-input" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Register Account</button>

                <p class="switch-text">
                    Already have an account? <a class="switch-link" onclick="toLogin()">Sign In</a>
                </p>
            </form>

            <div class="form-footer">
                <p class="footer-secure">Authentication secured by DepEd IT Division</p>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <span>•</span>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── Branding Panel (Sliding) ─── -->
    <div class="branding-panel" id="brandingSide">
        <div class="branding-bg">
            <div class="branding-blob"></div>
            <div class="branding-blob" style="animation-delay: -10s; background: rgba(168, 85, 247, 0.2); top: 50%; right: 10%;"></div>
            
            <div class="shape-container">
                <!-- Large Glowing Triangles -->
                <div class="shape shape-triangle size-xl"></div>
                <div class="shape shape-triangle size-lg"></div>
                <div class="shape shape-triangle size-xl" style="opacity: 0.05; --tri-size: 250px;"></div>
                
                <!-- Transparent Floating Circles -->
                <div class="shape shape-circle size-md"></div>
                <div class="shape shape-circle size-lg"></div>
                <div class="shape shape-circle size-sm"></div>
                <div class="shape shape-circle size-xl"></div>
                <div class="shape shape-circle size-md"></div>
                <div class="shape shape-circle size-sm"></div>
                <div class="shape shape-circle size-lg"></div>
            </div>
        </div>
        <div class="branding-content">
            <img src="{{ asset('images/Department_of_Education_(DepEd).svg.png') }}" alt="DepEd" class="brand-logo">
            <h2 class="brand-name">201 System</h2>
            <p class="brand-tagline">Personnel Information & Records Management System</p>
        </div>
        <div style="position: absolute; bottom: 2rem; color: rgba(255,255,255,0.4); font-size: 0.75rem; letter-spacing: 0.15em; text-transform: uppercase;">
            Department of Education • Philippines
        </div>
    </div>

</div>

<script>
    const wrapper = document.getElementById('authWrapper');

    function toLogin() {
        wrapper.classList.remove('show-register');
    }

    function toRegister() {
        wrapper.classList.add('show-register');
    }

    function togglePass(id, btn) {
        const el = document.getElementById(id);
        const isPassword = el.type === 'password';
        el.type = isPassword ? 'text' : 'password';
        
        if (isPassword) {
            btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
        } else {
            btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
        }
    }

    function checkStrength(pwd) {
        const bar = document.getElementById('strengthBar');
        const text = document.getElementById('strengthText');
        
        if (pwd.length === 0) {
            bar.style.width = '0%';
            text.innerText = '';
            return;
        }

        let strength = 0;
        
        if (pwd.length >= 8) strength++;
        if (/[A-Z]/.test(pwd)) strength++;
        if (/[0-9]/.test(pwd)) strength++;
        if (/[^A-Za-z0-9]/.test(pwd)) strength++;

        switch(strength) {
            case 0:
            case 1:
                bar.style.width = '25%';
                bar.style.background = '#ef4444';
                text.innerText = 'Weak';
                text.style.color = '#ef4444';
                break;
            case 2:
                bar.style.width = '50%';
                bar.style.background = '#f59e0b';
                text.innerText = 'Fair';
                text.style.color = '#f59e0b';
                break;
            case 3:
                bar.style.width = '75%';
                bar.style.background = '#3b82f6';
                text.innerText = 'Good';
                text.style.color = '#3b82f6';
                break;
            case 4:
                bar.style.width = '100%';
                bar.style.background = '#10b981';
                text.innerText = 'Strong';
                text.style.color = '#10b981';
                break;
        }
    }

    // Preserve state if error
    @if($errors->any())
        toRegister();
    @endif
</script>

</body>
</html>
