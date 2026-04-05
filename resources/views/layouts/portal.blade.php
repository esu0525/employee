<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>201 System Portal - @yield('title')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        :root {
            --portal-bg: #f8fafc;
            --portal-card: #ffffff;
            --portal-primary: #4f46e5;
            --portal-primary-soft: #eef2ff;
            --portal-text: #1e293b;
            --portal-text-muted: #64748b;
            --portal-border: #e2e8f0;
            --portal-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.05), 0 8px 10px -6px rgb(0 0 0 / 0.05);
            --portal-radius: 24px;
        }

        body {
            background-color: var(--portal-bg);
            color: var(--portal-text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* Abstract Background Elements */
        .bg-ornament {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 400px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            clip-path: polygon(0 0, 100% 0, 100% 60%, 0% 100%);
            z-index: -1;
            opacity: 0.03;
        }

        .portal-navbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .portal-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: var(--portal-text);
        }

        .logo-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .logo-text h1 {
            font-size: 1.25rem;
            font-weight: 800;
            margin: 0;
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.02em;
            background: linear-gradient(to right, #1e293b, #4f46e5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-text p {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--portal-text-muted);
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .portal-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 3rem 1.5rem;
            min-height: calc(100vh - 80px);
        }

        /* Premium Form Controls */
        .portal-card {
            background: var(--portal-card);
            border-radius: var(--portal-radius);
            box-shadow: var(--portal-shadow);
            border: 1px solid var(--portal-border);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .portal-input {
            width: 100%;
            padding: 0.875rem 1.25rem;
            background: #f8fafc;
            border: 2px solid #f1f5f9;
            border-radius: 14px;
            font-family: inherit;
            font-weight: 500;
            color: var(--portal-text);
            transition: all 0.2s ease;
        }

        .portal-input:focus {
            background: #ffffff;
            border-color: var(--portal-primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .portal-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 0.625rem;
            padding-left: 0.25rem;
        }

        .btn-portal-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 16px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
            text-decoration: none;
        }

        .btn-portal-primary:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4);
        }

        .btn-portal-secondary {
            background: white;
            color: #475569;
            padding: 0.625rem 1.25rem;
            border-radius: 12px;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .btn-portal-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .footer-portal {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--portal-text-muted);
            font-size: 0.875rem;
        }

        @media (max-width: 1024px) {
            .portal-container { max-width: 900px; }
        }

        @media (max-width: 768px) {
            .portal-container { padding: 2rem 1rem; }
            .bg-ornament { height: 300px; }
            .logo-text h1 { font-size: 1.125rem; }
        }

        @media (max-width: 640px) {
            .portal-container { padding: 1.5rem 0.75rem; }
            .portal-navbar { padding: 0.75rem 1rem; }
            .portal-logo { gap: 0.75rem; }
            .logo-icon { width: 38px; height: 38px; }
            .logo-text p { display: none; }
            .btn-portal-secondary { padding: 0.5rem 0.75rem; font-size: 0.75rem; }
        }
    </style>
</head>
<body>
    @if(!request()->has('compact'))
    <div class="bg-ornament"></div>
    @endif
    
    @if(!request()->has('compact'))
    <nav class="portal-navbar">
        <a href="{{ route('portal.index') }}" class="portal-logo">
            <div class="logo-icon">
                <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
            </div>
            <div class="logo-text">
                <h1>Service Center</h1>
                <p>SDO Quezon City</p>
            </div>
        </a>
        
        <div style="display: flex; gap: 1rem;">
            @if(!session()->has('auth_user_id'))
                <a href="{{ route('landing') }}" class="btn-portal-secondary">
                    <i data-lucide="home" style="width: 16px; height: 16px;"></i>
                    Home
                </a>
            @else
                @if(session('auth_user_role') !== 'viewer')
                    <a href="{{ route('dashboard') }}" class="btn-portal-secondary">
                        <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                        Return
                    </a>
                @endif
            @endif
        </div>
    </nav>
    @endif

    <!-- Security PIN Gate Modal -->
    <div id="pinGateModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(12px); z-index: 9999; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
        <div style="background: white; width: 100%; max-width: 440px; padding: 3rem; border-radius: 32px; box-shadow: var(--portal-shadow); text-align: center; transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
            <div style="width: 72px; height: 72px; background: var(--portal-primary-soft); color: var(--portal-primary); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <i data-lucide="shield-alert" style="width: 36px; height: 36px;"></i>
            </div>
            <h2 style="font-family: 'Outfit', sans-serif; font-size: 1.75rem; font-weight: 800; color: var(--portal-text); margin-bottom: 0.5rem;">Security Gate</h2>
            <p style="color: var(--portal-text-muted); font-size: 0.9375rem; margin-bottom: 2rem;">Enter 6-digit access code to proceed</p>
            
            <div style="display: flex; gap: 0.75rem; justify-content: center; margin-bottom: 2rem;">
                <input type="password" maxlength="1" class="pin-input" inputmode="numeric" pattern="[0-9]*" style="width: 44px; height: 56px; text-align: center; font-size: 1.5rem; font-weight: 800; border: 2px solid #e2e8f0; border-radius: 12px; background: #f8fafc; outline: none; transition: all 0.2s;" />
                <input type="password" maxlength="1" class="pin-input" inputmode="numeric" pattern="[0-9]*" style="width: 44px; height: 56px; text-align: center; font-size: 1.5rem; font-weight: 800; border: 2px solid #e2e8f0; border-radius: 12px; background: #f8fafc; outline: none; transition: all 0.2s;" />
                <input type="password" maxlength="1" class="pin-input" inputmode="numeric" pattern="[0-9]*" style="width: 44px; height: 56px; text-align: center; font-size: 1.5rem; font-weight: 800; border: 2px solid #e2e8f0; border-radius: 12px; background: #f8fafc; outline: none; transition: all 0.2s;" />
                <input type="password" maxlength="1" class="pin-input" inputmode="numeric" pattern="[0-9]*" style="width: 44px; height: 56px; text-align: center; font-size: 1.5rem; font-weight: 800; border: 2px solid #e2e8f0; border-radius: 12px; background: #f8fafc; outline: none; transition: all 0.2s;" />
                <input type="password" maxlength="1" class="pin-input" inputmode="numeric" pattern="[0-9]*" style="width: 44px; height: 56px; text-align: center; font-size: 1.5rem; font-weight: 800; border: 2px solid #e2e8f0; border-radius: 12px; background: #f8fafc; outline: none; transition: all 0.2s;" />
                <input type="password" maxlength="1" class="pin-input" inputmode="numeric" pattern="[0-9]*" style="width: 44px; height: 56px; text-align: center; font-size: 1.5rem; font-weight: 800; border: 2px solid #e2e8f0; border-radius: 12px; background: #f8fafc; outline: none; transition: all 0.2s;" />
            </div>

            <div id="pinError" style="color: #ef4444; font-size: 0.8125rem; font-weight: 700; margin-bottom: 1.5rem; display: none; animation: shake 0.4s ease-in-out;">
                Invalid access code. Please try again.
            </div>

            <div style="display: flex; gap: 1rem;">
                <button onclick="hidePinGate()" class="btn-portal-secondary" style="flex: 1; padding: 1rem;">Cancel</button>
                <button onclick="verifyPin()" class="btn-portal-primary" style="flex: 2; padding: 1rem; box-shadow: none;">Verify & Proceed</button>
            </div>
        </div>
    </div>

    <style>
        .pin-input:focus { border-color: var(--portal-primary) !important; background: white !important; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }
    </style>

    <main class="portal-container" style="{{ request()->has('compact') ? 'padding: 0; max-width: 100%; min-height: 0;' : '' }}">
        @yield('content')
    </main>

    @if(!request()->has('compact'))
    <footer class="footer-portal">
        <div style="margin-bottom: 2rem; display: flex; align-items: center; justify-content: center; opacity: 0.9;">
            <img src="{{ asset('images/logos/deped-wordmark.png') }}" alt="DepEd" style="height: 52px; filter: grayscale(0.5);">
        </div>
        <p>&copy; {{ date('Y') }} Schools Division Office - Quezon City. All Rights Reserved.</p>
        <p style="font-size: 0.75rem; margin-top: 0.5rem; opacity: 0.6;">Personnel Information & Records Management System (201 System)</p>
    </footer>
    @endif

    <script>
        lucide.createIcons();

        // New PIN Input Logic
        const pinInputs = document.querySelectorAll('.pin-input');
        pinInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.inputType === 'deleteContentBackward') return;
                if (input.value && index < pinInputs.length - 1) {
                    pinInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    pinInputs[index - 1].focus();
                } else if (e.key === 'Enter') {
                    verifyPin();
                }
            });
        });

        function showPinGate() {
            const modal = document.getElementById('pinGateModal');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.style.opacity = '1';
                modal.children[0].style.transform = 'scale(1)';
                pinInputs[0].focus();
            }, 10);
        }

        function hidePinGate() {
            const modal = document.getElementById('pinGateModal');
            modal.style.opacity = '0';
            modal.children[0].style.transform = 'scale(0.9)';
            setTimeout(() => { modal.style.display = 'none'; }, 300);
            pinInputs.forEach(i => i.value = '');
            document.getElementById('pinError').style.display = 'none';
        }

        function verifyPin() {
            const pin = Array.from(pinInputs).map(i => i.value).join('');
            const CORRECT_PIN = '123456'; 
            
            if (pin === CORRECT_PIN) {
                window.location.href = "{{ route('login') }}";
            } else {
                const error = document.getElementById('pinError');
                error.style.display = 'block';
                pinInputs.forEach(i => {
                    i.value = '';
                    i.style.borderColor = '#ef4444';
                });
                pinInputs[0].focus();
                setTimeout(() => {
                    pinInputs.forEach(i => i.style.borderColor = '#e2e8f0');
                }, 1000);
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
