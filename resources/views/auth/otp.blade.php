<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - 201 System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #4f46e5;
            --accent: #7c3aed;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
            --bg: #3b3a7a;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* ─── Animated Background ─── */
        .bg-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
        }

        /* Gradient mesh */
        .bg-canvas::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 30%, rgba(99,102,241,0.6) 0%, transparent 60%),
                radial-gradient(circle at 80% 70%, rgba(139,92,246,0.5) 0%, transparent 60%),
                radial-gradient(circle at 50% 10%, rgba(168,85,247,0.4) 0%, transparent 50%),
                radial-gradient(circle at 30% 90%, rgba(79,70,229,0.5) 0%, transparent 60%);
            animation: meshShift 10s ease-in-out infinite alternate;
        }

        @keyframes meshShift {
            0%   { filter: hue-rotate(0deg) brightness(1); }
            50%  { filter: hue-rotate(15deg) brightness(1.1); }
            100% { filter: hue-rotate(-10deg) brightness(0.9); }
        }

        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            opacity: 0;
            animation: particleFly linear infinite;
        }

        @keyframes particleFly {
            0%   { opacity: 0; transform: translateY(110vh) scale(0); }
            10%  { opacity: 0.6; }
            90%  { opacity: 0.3; }
            100% { opacity: 0; transform: translateY(-10vh) scale(1); }
        }

        /* Grid lines */
        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.08) 1px, transparent 1px);
            background-size: 50px 50px;
        }
        /* ─── OTP Card ─── */
        .otp-card {
            position: relative;
            z-index: 10;
            width: 480px;
            max-width: 95vw;
            background: rgba(0, 0, 0, 0.45); /* Flat Black Transparent */
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            padding: 2.5rem;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow:
                0 32px 80px rgba(0,0,0,0.6);
            animation: cardReveal 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardReveal {
            from { opacity: 0; transform: translateY(40px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ─── Header ─── */
        .otp-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .shield-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 20px;
            margin-bottom: 1.25rem;
            box-shadow: 0 12px 32px rgba(79,70,229,0.4);
            position: relative;
            animation: shieldPulse 3s ease-in-out infinite;
        }

        @keyframes shieldPulse {
            0%, 100% { box-shadow: 0 12px 32px rgba(79,70,229,0.4); }
            50%       { box-shadow: 0 12px 48px rgba(124,58,237,0.7), 0 0 0 8px rgba(79,70,229,0.1); }
        }

        .shield-icon svg {
            width: 36px;
            height: 36px;
            color: white;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.3));
        }

        .otp-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.5rem;
            letter-spacing: -0.03em;
        }

        .otp-subtitle {
            color: rgba(255,255,255,0.55);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .otp-email {
            color: rgba(167,139,250,0.9);
            font-weight: 600;
        }

        /* ─── Timer ─── */
        .timer-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.75rem;
        }

        .timer-icon {
            width: 16px;
            height: 16px;
            color: rgba(255,255,255,0.5);
        }

        .timer-display {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--warning);
            letter-spacing: 0.05em;
        }

        .timer-display.expired { color: var(--error); }

        .timer-label {
            font-size: 0.8125rem;
            color: rgba(255,255,255,0.4);
        }

        /* ─── OTP Input Boxes ─── */
        .otp-boxes {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .otp-field {
            width: 56px;
            height: 68px;
            background: rgba(255,255,255,0.06);
            border: 2px solid rgba(255,255,255,0.12);
            border-radius: 14px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.75rem;
            font-weight: 700;
            color: white;
            text-align: center;
            caret-color: transparent;
            outline: none;
            transition: all 0.2s ease;
            cursor: text;
        }

        .otp-field:focus {
            background: rgba(79,70,229,0.2);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79,70,229,0.2), 0 0 24px rgba(79,70,229,0.3);
            transform: scale(1.05);
        }

        .otp-field.filled {
            background: rgba(99,102,241,0.15);
            border-color: rgba(99,102,241,0.6);
        }

        .otp-field.error {
            background: rgba(239,68,68,0.12);
            border-color: var(--error);
            box-shadow: 0 0 0 4px rgba(239,68,68,0.15);
        }

        .otp-field.success {
            background: rgba(16,185,129,0.12);
            border-color: var(--success);
            box-shadow: 0 0 0 4px rgba(16,185,129,0.15);
        }

        /* Shake animation */
        @keyframes shake {
            0%,100% { transform: translateX(0) scale(1); }
            15%  { transform: translateX(-8px) scale(1.02); }
            30%  { transform: translateX(8px) scale(1.02); }
            45%  { transform: translateX(-6px); }
            60%  { transform: translateX(6px); }
            75%  { transform: translateX(-3px); }
            90%  { transform: translateX(3px); }
        }

        .otp-boxes.shake .otp-field { animation: shake 0.5s cubic-bezier(0.36,0.07,0.19,0.97); }

        /* ─── Status messages ─── */
        .otp-status {
            min-height: 24px;
            text-align: center;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            opacity: 1;
            visibility: visible;
        }

        .otp-status.error-msg  { color: var(--error); }
        .otp-status.success-msg { color: var(--success); }
        .otp-status.info-msg   { color: rgba(255,255,255,0.6); }

        .otp-status svg { width: 16px; height: 16px; flex-shrink: 0; }

        /* Attempts badges */
        .attempts-row {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-bottom: 1.5rem;
        }

        .attempt-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            transition: background 0.3s;
        }

        .attempt-dot.used { background: var(--error); }
        .attempt-dot.last { background: var(--warning); }

        /* ─── Verify Button ─── */
        .btn-verify {
            width: 100%;
            height: 3.25rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(79,70,229,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .btn-verify::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.12) 0%, transparent 100%);
        }

        .btn-verify:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(79,70,229,0.55);
        }

        .btn-verify:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-verify svg { width: 20px; height: 20px; }

        /* Loading dots */
        .loading-dots {
            display: none;
            gap: 5px;
        }

        .loading-dots span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: white;
            display: inline-block;
            animation: dotBounce 1.2s infinite;
        }

        .loading-dots span:nth-child(2) { animation-delay: 0.2s; }
        .loading-dots span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes dotBounce {
            0%,80%,100% { transform: scale(0.6); opacity: 0.4; }
            40%          { transform: scale(1); opacity: 1; }
        }

        /* ─── Resend ─── */
        .resend-row {
            text-align: center;
            font-size: 0.875rem;
            color: rgba(255,255,255,0.45);
        }

        .resend-btn {
            background: none;
            border: none;
            color: #a78bfa;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            font-size: 0.875rem;
            text-decoration: underline;
            text-underline-offset: 3px;
            transition: color 0.2s;
        }

        .resend-btn:hover:not(:disabled) { color: #c4b5fd; }
        .resend-btn:disabled { color: rgba(255,255,255,0.25); cursor: not-allowed; text-decoration: none; }

        /* ─── Lockout overlay ─── */
        .lockout-overlay {
            display: none;
            position: absolute;
            inset: 0;
            background: rgba(10,8,18,0.9);
            border-radius: 28px;
            z-index: 20;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            backdrop-filter: blur(8px);
        }

        .lockout-overlay.visible { display: flex; }

        .lockout-icon {
            font-size: 3rem;
            animation: lockBounce 1s ease-in-out infinite alternate;
        }

        @keyframes lockBounce {
            from { transform: translateY(0); }
            to   { transform: translateY(-8px); }
        }

        .lockout-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--error);
        }

        .lockout-desc {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
            text-align: center;
            max-width: 280px;
            line-height: 1.6;
        }

        .lockout-timer {
            font-family: 'JetBrains Mono', monospace;
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            letter-spacing: 0.05em;
        }

        .lockout-bar {
            width: 200px;
            height: 4px;
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .lockout-progress {
            height: 100%;
            background: linear-gradient(90deg, var(--error), var(--warning));
            border-radius: 4px;
            transition: width 1s linear;
        }

        /* Copy hint */
        .copy-hint {
            text-align: center;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.3);
            margin-bottom: 1rem;
        }

        /* Back link */
        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            margin-top: 1.25rem;
            font-size: 0.8125rem;
            color: rgba(255,255,255,0.35);
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover { color: rgba(255,255,255,0.7); }
        .back-link svg { width: 14px; height: 14px; }

        @media (max-width: 480px) {
            .otp-card { padding: 1.5rem; border-radius: 20px; }
            .shield-icon { width: 60px; height: 60px; margin-bottom: 1rem; }
            .shield-icon svg { width: 28px; height: 28px; }
            .otp-title { font-size: 1.5rem; }
            .otp-boxes { gap: 6px; }
            .otp-field { width: 45px; height: 55px; font-size: 1.5rem; border-radius: 10px; }
            .btn-verify { height: 3rem; font-size: 0.9375rem; }
        }
    </style>
</head>
<body>

    <!-- Animated bg -->
    <div class="bg-canvas">
        <div class="bg-grid"></div>
    </div>

    <!-- Particles (created by JS) -->
    <div id="particleContainer" style="position:fixed;inset:0;z-index:1;pointer-events:none;"></div>

    <!-- OTP Card -->
    <div class="otp-card" id="otpCard">

        <!-- Lockout overlay -->
        <div class="lockout-overlay" id="lockoutOverlay">
            <div class="lockout-icon">🔒</div>
            <div class="lockout-title">Account Locked</div>
            <div class="lockout-desc">Too many failed attempts. Please wait before trying again.</div>
            <div class="lockout-timer" id="lockoutTimer">5:00</div>
            <div class="lockout-bar">
                <div class="lockout-progress" id="lockoutProgress" style="width:100%"></div>
            </div>
        </div>

        <!-- Header -->
        <div class="otp-header">
            <div class="shield-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </div>
            <h1 class="otp-title">Verify Your Identity</h1>
            <p class="otp-subtitle">
                We sent a 6-digit code to<br>
                <span class="otp-email" id="otpEmailDisplay">your registered email</span>
            </p>
        </div>

        <!-- Timer -->
        <div class="timer-row">
            <svg class="timer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            <span class="timer-display" id="otpTimer">10:00</span>
            <span class="timer-label">remaining</span>
        </div>

        <!-- OTP input boxes -->
        <form id="otpForm">
            @csrf
            <div class="otp-boxes" id="otpBoxes">
                <input class="otp-field" type="text" inputmode="numeric" maxlength="1" id="otp0" autocomplete="off" data-idx="0">
                <input class="otp-field" type="text" inputmode="numeric" maxlength="1" id="otp1" autocomplete="off" data-idx="1">
                <input class="otp-field" type="text" inputmode="numeric" maxlength="1" id="otp2" autocomplete="off" data-idx="2">
                <input class="otp-field" type="text" inputmode="numeric" maxlength="1" id="otp3" autocomplete="off" data-idx="3">
                <input class="otp-field" type="text" inputmode="numeric" maxlength="1" id="otp4" autocomplete="off" data-idx="4">
                <input class="otp-field" type="text" inputmode="numeric" maxlength="1" id="otp5" autocomplete="off" data-idx="5">
            </div>

            <div class="copy-hint">💡 You can paste your OTP code directly</div>

            <!-- Status message -->
            <div class="otp-status info-msg" id="otpStatus">
                Enter the 6-digit code from your email
            </div>

            <!-- Attempt dots -->
            <div class="attempts-row" id="attemptsRow">
                <div class="attempt-dot" id="dot0"></div>
                <div class="attempt-dot" id="dot1"></div>
                <div class="attempt-dot" id="dot2"></div>
                <div class="attempt-dot" id="dot3"></div>
                <div class="attempt-dot" id="dot4"></div>
            </div>

            <!-- Verify button -->
            <button type="submit" class="btn-verify" id="verifyBtn">
                <div class="loading-dots" id="verifyLoading">
                    <span></span><span></span><span></span>
                </div>
                <svg id="verifyIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
                <span id="verifyText">Verify OTP</span>
            </button>

            <div class="resend-row">
                Didn't receive the code?
                <button type="button" class="resend-btn" id="resendBtn" onclick="resendOtp()">Resend OTP</button>
            </div>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to Login
        </a>
    </div>

    <script>
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const VERIFY_URL = '{{ route("auth.otp.verify") }}';
        const RESEND_URL = '{{ route("auth.otp.resend") }}';
        const MAX_ATTEMPTS = 5;

        // PHP injected values for persistent lockout
        const serverIsLocked = {{ $isLocked ? 'true' : 'false' }};
        const serverLockedSeconds = {{ $lockedSeconds }};

        // ─── Particles ───────────────────────────────────────────────────────
        const colors = ['#ffffff','#cbd5e1','#94a3b8','#e2e8f0','#6366f1','#8b5cf6'];
        const pc = document.getElementById('particleContainer');
        for (let i = 0; i < 30; i++) {
            const el = document.createElement('div');
            el.className = 'particle';
            const size = Math.random() * 6 + 2;
            el.style.cssText = `
                width: ${size}px; height: ${size}px;
                background: ${colors[Math.floor(Math.random()*colors.length)]};
                left: ${Math.random()*100}%;
                animation-duration: ${8 + Math.random()*12}s;
                animation-delay: ${-Math.random()*15}s;
            `;
            pc.appendChild(el);
        }

        // ─── OTP Timer (10 min) ───────────────────────────────────────────────
        let otpSeconds = 10 * 60;
        let timerInterval;

        function startOtpTimer() {
            timerInterval = setInterval(() => {
                otpSeconds--;
                if (otpSeconds <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('otpTimer').textContent = 'EXPIRED';
                    document.getElementById('otpTimer').classList.add('expired');
                    showStatus('OTP has expired. Please request a new one.', 'error');
                    document.getElementById('verifyBtn').disabled = true;
                    return;
                }
                const m = Math.floor(otpSeconds / 60).toString().padStart(2, '0');
                const s = (otpSeconds % 60).toString().padStart(2, '0');
                document.getElementById('otpTimer').textContent = `${m}:${s}`;
            }, 1000);
        }

        startOtpTimer();

        // ─── OTP Box Logic ────────────────────────────────────────────────────
        const boxes = document.querySelectorAll('.otp-field');

        boxes.forEach((box, i) => {
            // Allow navigation with keyboard
            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace') {
                    e.preventDefault();
                    if (box.value) {
                        box.value = '';
                        box.classList.remove('filled');
                    } else if (i > 0) {
                        boxes[i - 1].focus();
                        boxes[i - 1].value = '';
                        boxes[i - 1].classList.remove('filled');
                    }
                    clearError();
                } else if (e.key === 'ArrowLeft' && i > 0) {
                    boxes[i - 1].focus();
                } else if (e.key === 'ArrowRight' && i < 5) {
                    boxes[i + 1].focus();
                } else if (e.key === 'Delete') {
                    box.value = '';
                    box.classList.remove('filled');
                    clearError();
                }
            });

            box.addEventListener('input', (e) => {
                let val = e.target.value.replace(/\D/g, '').slice(0, 1);

                // Handle paste of multiple digits via input event
                const rawInput = e.target.value;
                if (rawInput.length > 1) {
                    const digits = rawInput.replace(/\D/g, '').slice(0, 6);
                    distributePaste(digits, i);
                    return;
                }

                box.value = val;
                clearError();

                if (val) {
                    box.classList.add('filled');
                    if (i < 5) boxes[i + 1].focus();
                    autoVerifyIfComplete();
                } else {
                    box.classList.remove('filled');
                }
            });

            box.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                distributePaste(pasted, i);
            });

            box.addEventListener('focus', () => {
                box.select();
            });
        });

        function distributePaste(digits, startIdx) {
            for (let j = 0; j < digits.length && startIdx + j < 6; j++) {
                boxes[startIdx + j].value = digits[j];
                boxes[startIdx + j].classList.add('filled');
            }
            const nextFocus = Math.min(startIdx + digits.length, 5);
            boxes[nextFocus].focus();
            clearError();
            autoVerifyIfComplete();
        }

        function getOtpValue() {
            return Array.from(boxes).map(b => b.value).join('');
        }

        function autoVerifyIfComplete() {
            const val = getOtpValue();
            if (val.length === 6) {
                setTimeout(() => submitOtp(val), 200);
            }
        }

        function clearError() {
            boxes.forEach(b => b.classList.remove('error', 'success'));
        }

        // ─── Status messages ──────────────────────────────────────────────────
        let statusTimeout = null;
        function showStatus(msg, type, persistent = false) {
            const el = document.getElementById('otpStatus');
            el.className = `otp-status ${type}-msg`;
            el.style.opacity = '1';
            el.style.visibility = 'visible';
            
            const icons = {
                error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
                success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>',
                info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
            };
            el.innerHTML = (icons[type] || '') + msg;

            if (statusTimeout) clearTimeout(statusTimeout);
            if (!persistent) {
                statusTimeout = setTimeout(() => {
                    el.style.opacity = '0';
                    el.style.visibility = 'hidden';
                }, 4000); // Fades out after 4 seconds
            }
        }

        // ─── Attempt Dots ─────────────────────────────────────────────────────
        function updateDots(attempts) {
            for (let i = 0; i < MAX_ATTEMPTS; i++) {
                const dot = document.getElementById('dot' + i);
                dot.classList.remove('used', 'last');
                if (i < attempts) {
                    dot.classList.add(i === attempts - 1 && attempts < MAX_ATTEMPTS ? 'last' : 'used');
                }
            }
        }

        // ─── Shake animation ──────────────────────────────────────────────────
        function shakeBoxes() {
            const boxesRow = document.getElementById('otpBoxes');
            boxes.forEach(b => b.classList.add('error'));
            boxesRow.classList.add('shake');
            setTimeout(() => boxesRow.classList.remove('shake'), 600);
        }

        // ─── Verify OTP ───────────────────────────────────────────────────────
        document.getElementById('otpForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const val = getOtpValue();
            if (val.length < 6) {
                shakeBoxes();
                showStatus('Please enter all 6 digits.', 'error');
                return;
            }
            submitOtp(val);
        });

        function setVerifyLoading(loading) {
            document.getElementById('verifyLoading').style.display = loading ? 'flex' : 'none';
            document.getElementById('verifyIcon').style.display    = loading ? 'none' : '';
            document.getElementById('verifyText').textContent      = loading ? 'Verifying…' : 'Verify OTP';
            document.getElementById('verifyBtn').disabled          = loading;
        }

        function submitOtp(code) {
            setVerifyLoading(true);

            fetch(VERIFY_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ otp: code }),
            })
            .then(r => {
                if (!r.ok && r.status !== 422 && r.status !== 401) {
                    throw new Error('Server error');
                }
                return r.json();
            })
            .then(data => {
                setVerifyLoading(false);

                if (data.success) {
                    boxes.forEach(b => b.classList.add('success'));
                    showStatus('✅ Verification successful! Redirecting…', 'success');
                    document.getElementById('verifyBtn').disabled = true;
                    // Use replace to avoid back behavior
                    setTimeout(() => { window.location.replace(data.redirect); }, 400);
                    clearInterval(timerInterval);
                    return;
                }

                if (data.locked) {
                    startLockout(data.seconds);
                    return;
                }

                if (data.expired) {
                    showStatus('OTP has expired. Please request a new code.', 'error');
                    shakeBoxes();
                    return;
                }

                // Wrong OTP
                shakeBoxes();
                showStatus(data.message || 'Invalid OTP code.', 'error');
                updateDots(data.attempts || 0);

                // Clear boxes and re-focus
                boxes.forEach(b => { b.value = ''; b.classList.remove('filled'); });
                boxes[0].focus();
            })
            .catch(() => {
                setVerifyLoading(false);
                showStatus('Network error. Please try again.', 'error');
            });
        }

        // ─── Lockout ──────────────────────────────────────────────────────────
        let lockoutInterval = null;

        function startLockout(seconds) {
            const overlay  = document.getElementById('lockoutOverlay');
            const timerEl  = document.getElementById('lockoutTimer');
            const barEl    = document.getElementById('lockoutProgress');
            const totalSec = seconds;

            overlay.classList.add('visible');

            if (lockoutInterval) clearInterval(lockoutInterval);

            let remaining = seconds;
            function tick() {
                const m = Math.floor(remaining / 60).toString().padStart(2, '0');
                const s = Math.floor(remaining % 60).toString().padStart(2, '0');
                timerEl.textContent  = `${m}:${s}`;
                barEl.style.width    = `${(remaining / totalSec) * 100}%`;

                if (remaining <= 0) {
                    clearInterval(lockoutInterval);
                    overlay.classList.remove('visible');
                    boxes.forEach(b => { b.value = ''; b.classList.remove('error','filled'); });
                    boxes[0].focus();
                    showStatus('You can try again now.', 'info');
                    updateDots(0);
                }
                remaining--;
            }

            tick();
            lockoutInterval = setInterval(tick, 1000);
        }

        // ─── Resend OTP ───────────────────────────────────────────────────────
        let resendCooldown = false;

        function resendOtp() {
            if (resendCooldown) return;

            const btn = document.getElementById('resendBtn');
            btn.disabled = true;
            btn.textContent = 'Sending…';

            const controller = new AbortController();
            const timeoutId  = setTimeout(() => controller.abort(), 35000); // 35s timeout

            fetch(RESEND_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({}),
                signal: controller.signal
            })
            .then(r => {
                clearTimeout(timeoutId);
                if (!r.ok) throw new Error('Network response was not ok');
                return r.json();
            })
            .then(data => {
                if (data.success) {
                    showStatus('New OTP sent to your email!', 'success');
                    otpSeconds = 10 * 60;
                    clearInterval(timerInterval);
                    startOtpTimer();
                    boxes.forEach(b => { b.value = ''; b.classList.remove('filled','error','success'); });
                    updateDots(0);
                    document.getElementById('verifyBtn').disabled = false;
                    boxes[0].focus();
                } else {
                    showStatus(data.message || 'Failed to resend OTP.', 'error');
                    btn.disabled = false;
                    btn.textContent = 'Resend OTP';
                }

                // 30-second cooldown for resend success
                if (data.success) {
                    resendCooldown = true;
                    let cd = 30;
                    const cdInterval = setInterval(() => {
                        btn.textContent = `Resend OTP (${cd}s)`;
                        cd--;
                        if (cd < 0) {
                            clearInterval(cdInterval);
                            btn.textContent = 'Resend OTP';
                            btn.disabled    = false;
                            resendCooldown  = false;
                        }
                    }, 1000);
                }
            })
            .catch(error => {
                clearTimeout(timeoutId);
                console.error('Resend Error:', error);
                if (error.name === 'AbortError') {
                    showStatus('Request timed out. Please try again.', 'error');
                } else {
                    showStatus('Request failed. Check your internet or try again later.', 'error');
                }
                btn.textContent = 'Resend OTP';
                btn.disabled    = false;
            });
        }

        // Focus first box on load or show lockout
        window.addEventListener('DOMContentLoaded', () => {
            if (serverIsLocked) {
                startLockout(serverLockedSeconds);
            } else {
                boxes[0].focus();

                // Silent initial resend if flag is set (Triggered after login)
                @if($triggerResend)
                    console.log('Triggering initial OTP send asynchronously...');
                    resendOtp();
                @endif
            }
        });
    </script>
</body>
</html>
