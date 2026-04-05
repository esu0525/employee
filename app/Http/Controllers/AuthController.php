<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 10;
    private const MAX_OTP_ATTEMPTS = 5;
    private const OTP_LOCKOUT_MINUTES = 3;

    // ─── Show Login/Register Page ─────────────────────────────────────────────

    public function showLogin()
    {
        if (session()->has('auth_user_id')) {
            return redirect()->route('dashboard');
        }

        $lockedUntil = session('otp_locked_until');
        if ($lockedUntil && now()->greaterThanOrEqualTo($lockedUntil)) {
            session(['otp_attempts' => 0, 'otp_locked_until' => null]);
            $lockedUntil = null;
        }
        $isLocked = $lockedUntil && now()->lessThan($lockedUntil);
        $lockedSeconds = $isLocked ? now()->diffInSeconds($lockedUntil) : 0;

        return view('auth.login', [
            'isLocked' => $isLocked,
            'lockedSeconds' => $lockedSeconds
        ]);
    }

    // ─── Login ────────────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!($user instanceof User) || !Hash::check($request->password, $user->password)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email or password.'
                ], 401);
            }
            return back()->withInput($request->only('email'))
                ->with('error', 'Invalid email or password.');
        }

        // Check for persistent lockout from session
        $lockedUntil = session('otp_locked_until');
        if ($lockedUntil) {
            if (now()->lessThan($lockedUntil)) {
                $remaining = now()->diffInSeconds($lockedUntil);
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'locked' => true,
                        'seconds' => $remaining,
                        'message' => 'Too many failed attempts. Try again in ' . ceil($remaining / 60) . ' minute(s).',
                    ]);
                }
                return back()->with('error', 'Account locked. Try again later.');
            } else {
                // Lockout has expired - clear it and reset attempts
                session(['otp_attempts' => 0, 'otp_locked_until' => null]);
            }
        }

        // Invalidate any old OTPs for this user
        OtpCode::where('user_id', $user->id)->update(['used' => true]);

        // Generate new 6-digit OTP
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'used' => false,
        ]);

        // Store temp session data
        session([
            'otp_user_id' => $user->id,
            'remember'    => $request->has('remember'),
            'otp_attempts'  => 0,
            // Flag disabled as we trigger background send directly in this method now
            'trigger_initial_resend' => false, 
        ]);
        session()->save();

        // ─── Fire Background OTP Dispatch ──────────────────────────────────────
        // This makes the login button fast but starts the email process immediately
        try {
            $php = defined('PHP_BINARY') ? PHP_BINARY : 'php';
            $artisan = base_path('artisan');
            
            \Log::info("Login-based async OTP for {$user->email} using $code");

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $cmd = "start /B \"\" \"$php\" \"$artisan\" app:send-otp {$user->id} $code > nul 2>&1";
                pclose(popen($cmd, "r"));
            } else {
                $cmd = "\"$php\" \"$artisan\" app:send-otp {$user->id} $code > /dev/null 2>&1 &";
                exec($cmd);
            }
        } catch (\Exception $e) {
            \Log::error('Background OTP trigger error in login(): ' . $e->getMessage());
        }

        // Return JSON success or redirect
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'email' => $user->email,
                'message' => 'Preparing verification screen...',
            ]);
        }

        return redirect()->route('auth.otp');
    }

    // ─── Register ─────────────────────────────────────────────────────────────

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'viewer',
        ]);

        return redirect()->route('login')
            ->with('success', 'Account created successfully! You can now sign in.');
    }

    // ─── Show OTP Page ────────────────────────────────────────────────────────

    public function showOtp()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $remainingLockSeconds = 0;
        $isLocked = false;
        $lockedUntil = session('otp_locked_until');
        if ($lockedUntil && now()->greaterThanOrEqualTo($lockedUntil)) {
            session(['otp_attempts' => 0, 'otp_locked_until' => null]);
            $lockedUntil = null;
        }

        if ($lockedUntil && now()->lessThan($lockedUntil)) {
            $isLocked = true;
            $remainingLockSeconds = now()->diffInSeconds($lockedUntil);
        }

        // Check if we should trigger the initial email (and clear it from session)
        $triggerResend = session()->pull('trigger_initial_resend', false);

        return view('auth.otp', [
            'isLocked' => $isLocked,
            'lockedSeconds' => $remainingLockSeconds,
            'triggerResend' => $triggerResend
        ]);
    }

    // ─── Verify OTP ───────────────────────────────────────────────────────────

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $userId = session('otp_user_id');

        // Check lockout
        $lockedUntil = session('otp_locked_until');
        if ($lockedUntil) {
            if (now()->lessThan($lockedUntil)) {
                $remaining = now()->diffInSeconds($lockedUntil);
                return response()->json([
                    'success' => false,
                    'locked' => true,
                    'seconds' => $remaining,
                    'message' => 'Too many attempts. Try again in ' . ceil($remaining / 60) . ' minute(s).',
                ]);
            } else {
                // Lockout has expired - clear it and reset attempts
                session(['otp_attempts' => 0, 'otp_locked_until' => null]);
            }
        }

        $otp = OtpCode::where('user_id', $userId)
            ->where('used', false)
            ->orderByDesc('created_at')
            ->first();

        if (!$otp || $otp->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please login again.',
                'expired' => true,
            ]);
        }

        if ($otp->code !== $request->otp) {
            $attempts = session('otp_attempts', 0) + 1;
            session(['otp_attempts' => $attempts]);

            $remaining = self::MAX_OTP_ATTEMPTS - $attempts;

            if ($attempts >= self::MAX_OTP_ATTEMPTS) {
                $lockUntil = now()->addMinutes(self::OTP_LOCKOUT_MINUTES);
                session(['otp_locked_until' => $lockUntil]);

                return response()->json([
                    'success' => false,
                    'locked' => true,
                    'seconds' => self::OTP_LOCKOUT_MINUTES * 60,
                    'message' => 'Maximum attempts reached. Locked for ' . self::OTP_LOCKOUT_MINUTES . ' minutes.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => "Invalid OTP. {$remaining} attempt(s) remaining.",
                'attempts' => $attempts,
                'max' => self::MAX_OTP_ATTEMPTS,
            ]);
        }

        // Valid OTP
        $otp->update(['used' => true]);

        $user = User::find($userId);

        // Use standard Auth logic for "Keep me logged in"
        $remember = session('remember', false);
        Auth::login($user, $remember);

        // Clear OTP session, set additional auth markers
        session()->forget(['otp_user_id', 'otp_attempts', 'otp_locked_until', 'remember']);
        session([
            'auth_user_id' => $user->id,
            'auth_user_name' => $user->name,
            'auth_user_email' => $user->email,
            'auth_user_role' => $user->role,
            'welcome_name' => $user->name,
            'welcome_avatar' => $user->profile_picture,
        ]);
        session()->flash('show_welcome_modal', true);

        // Record the login log
        \App\Models\ActivityLog::log('login', 'auth', 'User logged into the system');

        // Update last login timestamp on user record
        $user->update(['last_login_at' => now()]);

        // Force session save to ensure markers are persistent for the next request (Dashboard)
        session()->save();

        return response()->json([
            'success' => true,
            'redirect' => route('dashboard'),
        ], 200);
    }

    // ─── Resend OTP ───────────────────────────────────────────────────────────

    public function resendOtp()
    {
        if (!session()->has('otp_user_id')) {
            return response()->json(['success' => false, 'message' => 'Your verification session has expired. Please login again.']);
        }

        $userId = session('otp_user_id');
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User account not found.']);
        }

        // Invalidate older unused tokens first
        \App\Models\OtpCode::where('user_id', $userId)->where('used', false)->update(['used' => true]);

        // Generate new 6-digit OTP
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        \App\Models\OtpCode::create([
            'user_id' => $userId,
            'code' => $code,
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'used' => false,
        ]);

        session(['otp_attempts' => 0, 'otp_locked_until' => null]);

        try {
            $php = defined('PHP_BINARY') ? PHP_BINARY : 'php';
            $artisan = base_path('artisan');
            
            \Log::info("Triggering async OTP for {$user->email} using $code");

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows Background execution: start /B with escaped paths
                $cmd = "start /B \"\" \"$php\" \"$artisan\" app:send-otp $userId $code > nul 2>&1";
                pclose(popen($cmd, "r"));
            } else {
                // Linux Background execution: & with escaped paths
                $cmd = "\"$php\" \"$artisan\" app:send-otp $userId $code > /dev/null 2>&1 &";
                exec($cmd);
            }

            return response()->json([
                'success' => true,
                'message' => 'New 6-digit code has been sent. Please check your inbox.',
            ]);
        } catch (\Exception $e) {
            \Log::error('OTP Background trigger failure: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'System error. Please try resending manually.'], 500);
        }
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout()
    {
        \App\Models\ActivityLog::log('logout', 'auth', 'User logged out of the system');
        session()->flush();
        return redirect()->route('landing');
    }

    // ─── Send OTP Email via PHPMailer ─────────────────────────────────────────

    private function sendOtpEmail(User $user, string $code): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host', env('MAIL_HOST', 'smtp.gmail.com'));
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.mailers.smtp.username', env('MAIL_USERNAME'));
            $mail->Password = config('mail.mailers.smtp.password', env('MAIL_PASSWORD'));
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
            $mail->Port       = config('mail.mailers.smtp.port', env('MAIL_PORT', 587));
            $mail->Timeout    = 45; // Further increased timeout
            $mail->SMTPKeepAlive = false; // Disable KeepAlive for single shot

            // Add certificate bypass for local/XAMPP environments
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom(
                config('mail.from.address', env('MAIL_FROM_ADDRESS')),
                config('mail.from.name', env('MAIL_FROM_NAME', '201 System'))
            );
            $mail->addAddress($user->email, $user->name);

            $mail->isHTML(true);
            $mail->Subject = "201 System OTP Verification";
            $mail->Body = $this->buildOtpEmailHtml($user->name, $code);
            $mail->AltBody = "Hello {$user->name},\n\nYour OTP code is: {$code}\n\nThis code expires in 10 minutes.\n\nDo not share this code with anyone.";

            $mail->send();
            return true;
        }
        catch (MailException $e) {
            \Log::error('PHPMailer error: ' . $mail->ErrorInfo);
            return false;
        }
        catch (\Exception $e) {
            \Log::error('General email error: ' . $e->getMessage());
            return false;
        }
    }

    // ─── Email HTML Template ──────────────────────────────────────────────────

    private function buildOtpEmailHtml(string $name, string $code): string
    {
        $currentDate = now()->format('Y');
        $spacedCode = implode(' ', str_split($code));

        return "
        <div style=\"font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f6f9fc; padding: 40px 20px;\">
            <div style=\"max-width: 500px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);\">
                <!-- Header -->
                <div style=\"background: #4338ca; padding: 35px 20px; text-align: center;\">
                    <h1 style=\"color: #ffffff; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;\">201 System</h1>
                    <p style=\"color: #ffffff; margin: 8px 0 0 0; font-size: 13px; font-weight: 600; opacity: 0.9; letter-spacing: 2px;\">OFFICIAL AUTHENTICATION SERVICE</p>
                </div>
                
                <!-- Content -->
                <div style=\"padding: 40px 35px;\">
                    <p style=\"font-size: 16px; color: #1e293b; margin-bottom: 25px;\">Hello <strong>$name</strong>,</p>
                    <p style=\"font-size: 15px; color: #475569; line-height: 1.7; margin-bottom: 35px;\">
                        Your 201 System OTP is ready. Use the code below to complete your login. Thank you for visiting 201 System Portal. Do not share it with anyone.
                    </p>
                    
                    <!-- OTP Box -->
                    <div style=\"text-align: center; margin-bottom: 12px;\">
                        <div style=\"display: inline-block; background: #f8fafc; border: 1px solid #e2e8f0; padding: 25px 45px; border-radius: 16px;\">
                            <span style=\"font-size: 36px; font-weight: 800; color: #1e1b4b; letter-spacing: 1px; font-family: 'JetBrains Mono', 'Courier New', monospace;\">$spacedCode</span>
                        </div>
                    </div>
                    <p style=\"text-align: center; font-size: 13px; color: #94a3b8; margin-bottom: 35px;\">(Copy and paste this code into the verification form)</p>
                    
                    <p style=\"text-align: center; font-size: 15px; font-weight: 700; color: #ef4444; margin-bottom: 35px; text-transform: none;\">Expires in 10 minutes</p>
                    
                    <!-- Security Advisory -->
                    <div style=\"background: #fff1f2; border-left: 5px solid #ef4444; border-radius: 10px; padding: 18px 22px;\">
                        <p style=\"margin: 0; font-size: 14px; color: #991b1b; line-height: 1.6;\">
                            ⚠️ <strong>Security Advisory:</strong> This code is confidential. Never share your OTP with anyone. If you didn't request this, please secure your account immediately.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div style=\"text-align: center; margin-top: 35px;\">
                <p style=\"font-size: 13px; color: #94a3b8; margin: 0;\">© $currentDate DepEd Schools Division of Quezon City • ICT Division</p>
                <p style=\"font-size: 13px; color: #cbd5e1; margin: 8px 0 0 0;\">This is an automated system message. Do not reply.</p>
            </div>
        </div>";
    }

    // ─── Forgot Password Flow ────────────────────────────────────────────────

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'We could not find a user with that email address.');
        }

        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        ['token' => $token, 'created_at' => now()]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);

        $sent = $this->sendEmail($user->email, $user->name, 'Reset Password Link', $this->buildResetEmailHtml($user->name, $resetUrl));

        if ($sent) {
            return back()->with('success', 'We have emailed your password reset link!');
        }
        return back()->with('error', 'Failed to send reset link. Please try again later.');
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $entry = DB::table('password_reset_tokens')->where([
            'email' => $request->email,
            'token' => $request->token,
        ])->first();

        if (!$entry) {
            return back()->with('error', 'Invalid token or email.');
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect()->route('login')->with('success', 'Your password has been changed! You can now login.');
    }

    private function sendEmail($to, $name, $subject, $body)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
            $mail->Port = env('MAIL_PORT', 587);

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom(
                config('mail.from.address', env('MAIL_FROM_ADDRESS')),
                config('mail.from.name', env('MAIL_FROM_NAME'))
            );
            $mail->addAddress($to, $name);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();
            return true;
        }
        catch (MailException $e) {
            \Log::error('PHPMailer reset link error: ' . $mail->ErrorInfo);
            return false;
        }
    }

    private function buildResetEmailHtml($name, $url)
    {
        return "
        <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px;'>
            <h2 style='color: #1e293b;'>Reset Your Password</h2>
            <p>Hello {$name},</p>
            <p>You are receiving this email because we received a password reset request for your account.</p>
            <p style='margin: 30px 0;'>
                <a href='{$url}' style='background: #4f46e5; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold;'>Reset Password</a>
            </p>
            <p>This password reset link will expire in 60 minutes.</p>
            <p>If you did not request a password reset, no further action is required.</p>
            <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;'>
            <p style='font-size: 12px; color: #64748b;'>If you're having trouble clicking the \"Reset Password\" button, copy and paste the URL below into your web browser:</p>
            <p style='font-size: 12px; color: #4f46e5; word-break: break-all;'>{$url}</p>
        </div>";
    }
}
