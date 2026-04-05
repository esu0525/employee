<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - 201 System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --bg-main: #ffffff;
            --bg-secondary: #f8fafc;
            --input-bg: #f1f5f9;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --panel-bg: #312e81;
            --transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-main);
            height: 100vh;
            overflow: hidden;
            display: flex;
        }

        .auth-wrapper {
            display: flex;
            width: 100%;
            height: 100%;
        }

        /* ─── Form Section (Left) ─── */
        .form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4rem;
            background: white;
            z-index: 10;
        }

        .form-inner {
            width: 100%;
            max-width: 440px;
        }

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

        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main); font-size: 0.875rem; }
        
        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
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
            box-sizing: border-box;
        }
        
        .form-input:focus { background: white; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
        
        .btn-submit {
            width: 100%;
            height: 4rem;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.125rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }
        
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4); filter: brightness(1.1); }

        /* ─── Branding Section (Right) ─── */
        .branding-section {
            flex: 1;
            background: var(--panel-bg);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            overflow: hidden;
        }

        .bg-canvas { position: absolute; inset: 0; z-index: 1; overflow: hidden; }
        
        /* Animated Mesh Overlay */
        .bg-canvas::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 30%, rgba(99,102,241,0.6) 0%, transparent 60%),
                radial-gradient(circle at 80% 70%, rgba(139,92,246,0.5) 0%, transparent 60%),
                radial-gradient(circle at 50% 10%, rgba(168,85,247,0.4) 0%, transparent 50%);
            animation: meshShift 10s ease-in-out infinite alternate;
            z-index: 1;
        }

        /* White Grid Overlay */
        .bg-grid {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 2;
        }

        @keyframes meshShift { 0% { opacity: 0.7; transform: scale(1); } 100% { opacity: 1; transform: scale(1.1); } }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            pointer-events: none;
            z-index: 2;
            animation: floatUp linear infinite;
        }
        @keyframes floatUp { 0% { transform: translateY(110vh) scale(0); opacity: 0; } 10%  { opacity: 0.6; } 90%  { opacity: 0.3; } 100% { transform: translateY(-10vh) scale(1); opacity: 0; } }

        .branding-content { position: relative; z-index: 10; display: flex; flex-direction: column; align-items: center; }

        .brand-logo-container {
            width: 180px;
            height: 180px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            margin-bottom: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        }
        
        .brand-logo-container img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }

        .brand-title { font-family: 'Outfit'; font-size: 4.5rem; font-weight: 800; margin: 0; letter-spacing: -3px; line-height: 1; }
        .brand-subtitle { font-size: 1.25rem; opacity: 0.8; margin-top: 1rem; font-weight: 500; letter-spacing: 0.05em; text-transform: uppercase; }

        .alert { padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 2rem; font-size: 0.875rem; font-weight: 500; display: flex; align-items: center; gap: 0.75rem; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fff1f2; color: #991b1b; border: 1px solid #fecdd3; }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9375rem;
            margin-top: 2rem;
            transition: var(--transition);
        }
        .back-link:hover { color: var(--primary); transform: translateX(-4px); }

        @media (max-width: 1024px) {
            .branding-section { display: none; }
            .form-section { padding: 2rem; }
        }
    </style>
</head>
<body>

    <div class="auth-wrapper">
        <!-- Form Section -->
        <div class="form-section">
            <div class="form-inner">
                <h1 class="form-title">Forgot password?</h1>
                <p class="form-subtitle">No worries, we'll send you reset instructions. Please enter the email address linked to your account.</p>

                @if(session('success'))
                    <div class="alert alert-success">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Work Email</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </span>
                            <input type="email" name="email" class="form-input" placeholder="admin@deped.gov.ph" required autofocus>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Send Reset Link</button>
                </form>

                <div style="text-align: center;">
                    <a href="{{ route('login') }}" class="back-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        Back to login
                    </a>
                </div>
            </div>
        </div>

        <!-- Branding Section -->
        <div class="branding-section">
            <div class="bg-canvas"></div>
            <div class="bg-grid"></div>
            <div class="branding-content">
                <div class="brand-logo-container">
                    <img src="{{ asset('images/logos/HRNTP-logo.jpg') }}" alt="Logo">
                </div>
                <h2 class="brand-title">201 System</h2>
                <p class="brand-subtitle">Personnel Information & Records Management System</p>
            </div>
        </div>
    </div>

    <script>
        // Particle System
        const colors = ['#ffffff', '#cbd5e1', '#94a3b8', '#e2e8f0', '#6366f1', '#8b5cf6'];
        const canvas = document.querySelector('.bg-canvas');
        for (let i = 0; i < 40; i++) {
            const el = document.createElement('div');
            el.className = 'particle';
            const size = Math.random() * 8 + 4;
            el.style.cssText = `
                width: ${size}px; height: ${size}px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                left: ${Math.random() * 100}%;
                animation-duration: ${8 + Math.random() * 10}s;
                animation-delay: ${-Math.random() * 15}s;
                opacity: ${0.2 + Math.random() * 0.4};
            `;
            canvas.appendChild(el);
        }
    </script>
</body>
</html>
