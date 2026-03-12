<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DepEd 201 System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: #f8fafc;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        .login-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .login-left {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem;
            background: white;
            z-index: 10;
        }

        .login-right {
            flex: 1.2;
            position: relative;
            background: radial-gradient(circle at top right, #4f46e5 0%, #1e1b4b 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 4rem;
            text-align: center;
            overflow: hidden;
        }

        .login-right::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('https://www.deped.gov.ph/wp-content/uploads/2019/01/deped-logo-contact-us.png') center/contain no-repeat;
            opacity: 0.05;
            filter: grayscale(1) brightness(2);
        }

        .branding-content {
            position: relative;
            z-index: 2;
            animation: fadeInRight 1s ease-out;
        }

        .deped-logo-large {
            width: 320px;
            margin-bottom: 2rem;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.15)) brightness(2) saturate(1.4);
            animation: logoFloat 6s ease-in-out infinite;
            mix-blend-mode: lighten;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }

        .system-name {
            font-family: 'Outfit', sans-serif;
            font-size: 5rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 1rem;
            background: linear-gradient(to bottom, #ffffff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.02em;
        }

        .system-tagline {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            max-width: 400px;
            margin: 0 auto;
        }

        .login-form-container {
            width: 100%;
            max-width: 440px;
            animation: fadeInLeft 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .login-header {
            margin-bottom: 3rem;
        }

        .mobile-logo {
            display: none;
            margin-bottom: 2rem;
        }

        @media (max-width: 1024px) {
            .login-right { display: none; }
            .mobile-logo { display: block; }
        }

        .login-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.75rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.75rem;
            letter-spacing: -0.03em;
        }

        .login-subtitle {
            font-size: 1.125rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
        }

        .login-form .form-group {
            margin-bottom: 1.75rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-main);
            font-size: 0.875rem;
        }

        .login-input-wrapper {
            position: relative;
        }

        .login-input-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            transition: var(--transition);
        }

        .login-form .form-input {
            width: 100%;
            height: 3.75rem;
            padding: 0 1rem 0 3.5rem;
            background: #f8fafc;
            border: 2px solid transparent;
            border-radius: var(--radius-lg);
            font-size: 1rem;
            transition: var(--transition);
            color: var(--text-main);
        }

        .login-form .form-input:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .login-form .form-input:focus + .login-input-icon {
            color: var(--primary);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            font-size: 0.9375rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
        }

        .remember-me input {
            width: 1.2rem;
            height: 1.2rem;
            border-radius: 4px;
        }

        .btn-login {
            width: 100%;
            height: 4rem;
            font-size: 1.125rem;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: var(--radius-lg);
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
            transition: var(--transition);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 35px -5px rgba(99, 102, 241, 0.5);
        }

        .login-footer {
            margin-top: 3rem;
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #f1f5f9;
        }

        .login-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }

        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .bg-blobs {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .blob {
            position: absolute;
            width: 600px;
            height: 600px;
            background: rgba(99, 102, 241, 0.2);
            filter: blur(100px);
            border-radius: 50%;
            animation: float 20s infinite alternate;
        }

        @keyframes float {
            from { transform: translate(-20%, -20%) rotate(0deg); }
            to { transform: translate(20%, 20%) rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-left">
            <div class="bg-blobs">
                <div class="blob"></div>
            </div>
            
            <div class="login-form-container">
                <div class="mobile-logo">
                    <img src="{{ asset('images/Department_of_Education_(DepEd).svg.png') }}" alt="DepEd Logo" style="height: 60px;">
                </div>

                <div class="login-header">
                    <h1 class="login-title">Welcome back</h1>
                    <p class="login-subtitle">Please enter your credentials to access the 201 System</p>
                </div>

                <form action="{{ route('employees.index') }}" method="GET" class="login-form">
                    <div class="form-group">
                        <label class="form-label" for="email">Work Email</label>
                        <div class="login-input-wrapper">
                            <i data-lucide="mail" class="login-input-icon"></i>
                            <input type="email" id="email" class="form-input" placeholder="admin@deped.gov.ph" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="login-input-wrapper">
                            <i data-lucide="lock" class="login-input-icon"></i>
                            <input type="password" id="password" class="form-input" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="remember-forgot">
                        <label class="remember-me">
                            <input type="checkbox">
                            Keep me logged in
                        </label>
                        <a href="#" style="color: var(--primary); font-weight: 600; text-decoration: none;">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-login">
                        Sign In to System
                    </button>

                    <div style="margin-top: 1.5rem; text-align: center;">
                        <span style="font-size: 0.875rem; color: var(--text-muted);">Not an administrator?</span>
                        <a href="{{ route('portal.index') }}" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-top: 0.75rem; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: var(--radius-lg); text-decoration: none; color: var(--text-main); font-weight: 600; transition: var(--transition);" onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='var(--text-main)';" >
                            <i data-lucide="file-text" style="width: 18px; height: 18px;"></i>
                            Go to Request Form
                        </a>
                    </div>
                </form>

                <div class="login-footer">
                    <p style="color: var(--text-muted); font-size: 0.9375rem;">
                        Authentication secured by DepEd IT Division
                    </p>
                    <div style="margin-top: 1rem;">
                        <a href="#" style="font-size: 0.8125rem; color: var(--text-muted);">Privacy Policy</a>
                        <span style="margin: 0 0.5rem; color: #cbd5e1;">•</span>
                        <a href="#" style="font-size: 0.8125rem; color: var(--text-muted);">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="login-right">
            <div class="branding-content">
                <img src="{{ asset('images/Department_of_Education_(DepEd).svg.png') }}" alt="DepEd Logo" class="deped-logo-large" style="width: 480px;">
                <h2 class="system-name">201 System</h2>
                <p class="system-tagline">Personnel Information & Records Management System</p>
            </div>
            
            <div style="position: absolute; bottom: 3rem; color: rgba(255, 255, 255, 0.4); font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase;">
                Department of Education • Philippines
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
