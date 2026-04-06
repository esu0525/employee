/**
 * Authentication Module JavaScript
 * Handles login AJAX flow, OTP verification, lockout timers,
 * password visibility, strength checking, and background animations.
 */

document.addEventListener('DOMContentLoaded', function () {
    // Particles Animation
    const colors = ['#ffffff', '#cbd5e1', '#94a3b8', '#e2e8f0', '#6366f1', '#8b5cf6'];
    const pc = document.getElementById('particleContainer');
    if (pc) {
        for (let i = 0; i < 25; i++) {
            const el = document.createElement('div');
            el.className = 'particle';
            const size = Math.random() * 12 + 6;
            el.style.cssText = `
                width: ${size}px; height: ${size}px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                left: ${Math.random() * 100}%;
                animation-duration: ${8 + Math.random() * 12}s;
                animation-delay: ${-Math.random() * 15}s;
            `;
            pc.appendChild(el);
        }
    }

    // Login Form Submit
    const loginForm = document.querySelector('form[action*="login"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.clone().json().catch(() => ({}));
                
                if (data.success) {
                    showOTP();
                    if (data.message) showAlert(data.message, 'success');
                } else if (data.locked) {
                    showOTP();
                    showLockout(data.seconds);
                } else if (!data.success && data.message) {
                    showAlert(data.message, 'error');
                    resetLoginButton(btn);
                } else {
                    showAlert('Invalid email or password.', 'error');
                    resetLoginButton(btn);
                }
            })
            .catch(() => {
                showAlert('Connection error. Please try again.', 'error');
                resetLoginButton(btn);
            });
        });
    }

    // OTP Verification Submit
    const otpForm = document.getElementById('otpForm');
    if (otpForm) {
        otpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('verifyBtn');
            btn.disabled = true;
            btn.innerText = 'Verifying OTP...';

            fetch(window.LOG_VERIFY_URL || this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    btn.disabled = false;
                    btn.innerText = 'Verify & Access Account';
                    
                    if (data.locked) {
                        showLockout(data.seconds);
                    } else if (data.attempts) {
                        updateAttemptDots(data.attempts);
                        showAlert(data.message, 'error');
                        shakeOtpBoxes();
                    } else {
                        showAlert(data.message, 'error');
                    }
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerText = 'Verify & Sign In';
                showAlert('Verification failed. Try again.', 'error');
            });
        });
    }

    // OTP Input Navigation Logic
    const otpInputs = document.querySelectorAll('.otp-field');
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            if (e.inputType === 'deleteContentBackward') return;
            const val = input.value.replace(/\D/g, '');
            input.value = val;
            
            if (val && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
            updateFullCode();
            
            // Auto submit if all 6 boxes are filled
            if (Array.from(otpInputs).every(i => i.value.length === 1)) {
                const otpForm = document.getElementById('otpForm');
                const verifyBtn = document.getElementById('verifyBtn');
                if (otpForm && verifyBtn) {
                    verifyBtn.disabled = true;
                    verifyBtn.innerHTML = '<span class="animate-pulse">AUTO-VERIFYING...</span>';
                    // Small delay to let the user see the 6th digit
                    setTimeout(() => otpForm.requestSubmit(), 100);
                }
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            pastedData.split('').forEach((char, i) => {
                if (otpInputs[i]) {
                    otpInputs[i].value = char;
                }
            });
            updateFullCode();
            
            // Auto submit if all 6 boxes are filled after paste
            if (Array.from(otpInputs).every(i => i.value.length === 1)) {
                otpInputs[5].focus(); // Focus the last box
                
                const otpForm = document.getElementById('otpForm');
                const verifyBtn = document.getElementById('verifyBtn');
                if (otpForm && verifyBtn) {
                    verifyBtn.disabled = true;
                    verifyBtn.innerHTML = '<span class="animate-pulse">AUTO-VERIFYING...</span>';
                    setTimeout(() => otpForm.requestSubmit(), 100);
                }
            } else {
                // Focus the next empty box if not full
                const nextIndex = pastedData.length;
                if (otpInputs[nextIndex]) otpInputs[nextIndex].focus();
            }
        });
    });

    // Resend OTP Action
    const resendBtn = document.getElementById('resendBtn');
    
    window.handleResend = function() {
        if (!resendBtn || resendBtn.disabled) return;
        
        resendBtn.disabled = true;
        const originalText = resendBtn.innerText;
        resendBtn.innerText = 'Resending...';
        
        fetch(window.LOG_RESEND_URL || "/otp/resend", { 
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAlert('A new 6-digit code has been sent to your email.', 'success');
                startResendTimer();
                const otpInputs = document.querySelectorAll('.otp-field');
                otpInputs.forEach(f => {
                    f.value = '';
                    f.classList.remove('error-shake');
                });
                updateFullCode();
            } else {
                showAlert(data.message || 'Failed to resend OTP.', 'error');
                resendBtn.disabled = false;
                resendBtn.innerText = originalText;
            }
        })
        .catch(() => {
            showAlert('Connection error. Please try again.', 'error');
            resendBtn.disabled = false;
            resendBtn.innerText = originalText;
        });
    };

    window.hideOtp = function() {
        showLogin();
    };
});

function resetLoginButton(btn) {
    btn.disabled = false;
    btn.innerText = 'Sign In to System';
}

window.togglePass = function(id, btn) {
    const el = document.getElementById(id);
    if (!el) return;
    const isPassword = el.type === 'password';
    el.type = isPassword ? 'text' : 'password';
    
    if (isPassword) {
        btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
    } else {
        btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
    }
}

let otpInterval;
window.showOTP = function() {
    const wrapper = document.getElementById('authWrapper');
    if (wrapper) wrapper.classList.add('show-otp');
    startOtpTimer();
    startResendTimer();
    
    // Auto-trigger disabled as it is handled by server-side background process
    // handleResend();
}

window.showLogin = function() {
    const wrapper = document.getElementById('authWrapper');
    if (wrapper) wrapper.classList.remove('show-otp');
    clearInterval(otpInterval);
    
    const loginForm = document.querySelector('form[action*="login"]');
    if (loginForm) loginForm.reset();
    
    const loginBtn = document.querySelector('button[type="submit"]');
    if (loginBtn) resetLoginButton(loginBtn);
}

function startOtpTimer() {
    let otpSeconds = 10 * 60;
    const display = document.getElementById('otpTimerDisplay');
    if (!display) return;
    
    clearInterval(otpInterval);
    otpInterval = setInterval(() => {
        otpSeconds--;
        if (otpSeconds <= 0) {
            clearInterval(otpInterval);
            display.textContent = 'EXPIRED';
            return;
        }
        const m = Math.floor(otpSeconds / 60).toString().padStart(2, '0');
        const s = (otpSeconds % 60).toString().padStart(2, '0');
        display.textContent = `${m}:${s}`;
    }, 1000);
}

function startResendTimer() {
    const btn = document.getElementById('resendBtn');
    const display = document.getElementById('timer');
    if (!btn || !display) return;

    btn.disabled = true;
    let resendTimerCount = 30;
    display.innerText = resendTimerCount;
    
    const interval = setInterval(() => {
        resendTimerCount--;
        display.innerText = resendTimerCount;
        if (resendTimerCount <= 0) {
            clearInterval(interval);
            btn.disabled = false;
            btn.innerText = 'Resend now';
        }
    }, 1000);
}

window.showAlert = function(msg, type = 'error') {
    const placeholder = document.getElementById('alertPlaceholder');
    if (!placeholder) return;

    const alert = document.createElement('div');
    alert.className = `inline-alert ${type}`;
    
    const icon = type === 'success' 
        ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>'
        : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';
        
    alert.innerHTML = `${icon}<span>${msg}</span>`;
    placeholder.innerHTML = ''; 
    placeholder.appendChild(alert);
    
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        alert.style.transition = 'all 0.4s';
        setTimeout(() => alert.remove(), 400);
    }, 5000);
}

function updateAttemptDots(attempts) {
    const dots = document.querySelectorAll('.attempt-dot');
    dots.forEach((dot, i) => {
        if (i < attempts) {
            dot.style.background = '#ef4444';
        }
    });
}

function shakeOtpBoxes() {
    const wrapper = document.getElementById('otpBoxWrapper');
    const fields = document.querySelectorAll('.otp-field');
    if (!wrapper) return;
    
    wrapper.style.animation = 'none';
    wrapper.offsetHeight; 
    fields.forEach(f => f.classList.add('error-shake'));
    wrapper.style.animation = 'shake 0.5s';
    
    setTimeout(() => {
        fields.forEach(f => {
            f.classList.remove('error-shake');
            f.value = ''; // Auto-empty after shake
        });
        if (fields[0]) fields[0].focus();
        updateFullCode();
    }, 500);
}

window.showLockout = function(seconds) {
    const overlay = document.getElementById('lockoutOverlay');
    const timer = document.getElementById('lockoutTimerDisplay');
    const progress = document.getElementById('lockoutProgress');
    if (!overlay || !timer || !progress) return;

    overlay.classList.add('visible');
    let remaining = seconds;
    const totalDuration = 180; // 3 minutes total lockout duration
    
    // Initial update
    const updateDisplay = () => {
        const m = Math.floor(remaining / 60);
        const s = Math.floor(remaining % 60).toString().padStart(2, '0');
        timer.innerText = `${m}:${s}`;
        progress.style.width = `${(remaining / totalDuration) * 100}%`;
    };

    updateDisplay();
    
    const intv = setInterval(() => {
        remaining--;
        updateDisplay();
        
        if (remaining <= 0) {
            clearInterval(intv);
            overlay.classList.remove('visible');
            // Reset attempts and hide OTP
            const dots = document.querySelectorAll('.attempt-dot');
            dots.forEach(d => d.style.background = '#e2e8f0');
            hideOtp();
            location.reload(); // Hard reset to login
        }
    }, 1000);
}

function updateFullCode() {
    const otpInputs = document.querySelectorAll('.otp-field');
    const code = Array.from(otpInputs).map(i => i.value).join('');
    const fullOtp = document.getElementById('full_otp');
    if (fullOtp) fullOtp.value = code;
}

window.checkStrength = function(pwd) {
    const bar = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    if (!bar || !text) return;

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
            bar.style.width = '25%'; bar.style.background = '#ef4444';
            text.innerText = 'Weak'; text.style.color = '#ef4444';
            break;
        case 2:
            bar.style.width = '50%'; bar.style.background = '#f59e0b';
            text.innerText = 'Fair'; text.style.color = '#f59e0b';
            break;
        case 3:
            bar.style.width = '75%'; bar.style.background = '#3b82f6';
            text.innerText = 'Good'; text.style.color = '#3b82f6';
            break;
        case 4:
            bar.style.width = '100%'; bar.style.background = '#10b981';
            text.innerText = 'Strong'; text.style.color = '#10b981';
            break;
    }
}
