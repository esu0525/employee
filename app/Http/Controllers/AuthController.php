<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

class AuthController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 10;
    private const MAX_OTP_ATTEMPTS  = 5;
    private const OTP_LOCKOUT_MINUTES = 5;

    // ─── Show Login/Register Page ─────────────────────────────────────────────

    public function showLogin()
    {
        if (session()->has('auth_user_id')) {
            return redirect()->route('employees.index');
        }
        return view('auth.login');
    }

    // ─── Login ────────────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('email'))
                ->with('error', 'Invalid email or password.');
        }

        // Invalidate any old OTPs for this user
        OtpCode::where('user_id', $user->id)->update(['used' => true]);

        // Generate new 6-digit OTP
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id'    => $user->id,
            'code'       => $code,
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'used'       => false,
        ]);

        // Store temp session data
        session([
            'otp_user_id'  => $user->id,
            'otp_attempts' => 0,
            'otp_locked_until' => null,
        ]);

        // Send first OTP in background or via AJAX trigger on OTP page load
        // Simply redirect to OTP page now to make transition instant
        return redirect()->route('auth.otp')->with('trigger_initial_resend', true);
    }

    // ─── Register ─────────────────────────────────────────────────────────────

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'staff',
        ]);

        return redirect()->route('login')
            ->with('success', 'Account created successfully! You can now sign in.');
    }

    // ─── Show OTP Page ────────────────────────────────────────────────────────

    public function showOtp()
    {
        if (! session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $remainingLockSeconds = 0;
        $isLocked = false;
        $lockedUntil = session('otp_locked_until');

        if ($lockedUntil && now()->lessThan($lockedUntil)) {
            $isLocked = true;
            $remainingLockSeconds = now()->diffInSeconds($lockedUntil);
        }

        return view('auth.otp', [
            'isLocked' => $isLocked,
            'lockedSeconds' => $remainingLockSeconds
        ]);
    }

    // ─── Verify OTP ───────────────────────────────────────────────────────────

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if (! session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $userId = session('otp_user_id');

        // Check lockout
        $lockedUntil = session('otp_locked_until');
        if ($lockedUntil && now()->lessThan($lockedUntil)) {
            $remaining = now()->diffInSeconds($lockedUntil);
            return response()->json([
                'success'  => false,
                'locked'   => true,
                'seconds'  => $remaining,
                'message'  => 'Too many attempts. Try again in ' . ceil($remaining / 60) . ' minute(s).',
            ]);
        }

        $otp = OtpCode::where('user_id', $userId)
            ->where('used', false)
            ->orderByDesc('created_at')
            ->first();

        if (! $otp || $otp->isExpired()) {
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
                    'locked'  => true,
                    'seconds' => self::OTP_LOCKOUT_MINUTES * 60,
                    'message' => 'Maximum attempts reached. Locked for ' . self::OTP_LOCKOUT_MINUTES . ' minutes.',
                ]);
            }

            return response()->json([
                'success'   => false,
                'message'   => "Invalid OTP. {$remaining} attempt(s) remaining.",
                'attempts'  => $attempts,
                'max'       => self::MAX_OTP_ATTEMPTS,
            ]);
        }

        // Valid OTP
        $otp->update(['used' => true]);

        $user = User::find($userId);

        // Clear OTP session, set auth session
        session()->forget(['otp_user_id', 'otp_attempts', 'otp_locked_until']);
        session([
            'auth_user_id'   => $user->id,
            'auth_user_name' => $user->name,
            'auth_user_email'=> $user->email,
            'auth_user_role' => $user->role,
            'welcome_name'   => $user->name,
            'welcome_avatar' => $user->profile_picture,
        ]);
        session()->flash('show_welcome_modal', true);

        // Record the login log
        \App\Models\LoginLog::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json([
            'success'  => true,
            'redirect' => route('employees.index'),
        ], 200, ['Connection' => 'close']);
    }

    // ─── Resend OTP ───────────────────────────────────────────────────────────

    public function resendOtp()
    {
        if (! session()->has('otp_user_id')) {
            return response()->json(['success' => false, 'message' => 'Session expired.']);
        }

        $userId = session('otp_user_id');
        $user   = User::find($userId);

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not found.']);
        }

        OtpCode::where('user_id', $userId)->update(['used' => true]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id'    => $userId,
            'code'       => $code,
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'used'       => false,
        ]);

        session(['otp_attempts' => 0, 'otp_locked_until' => null]);

        $sent = $this->sendOtpEmail($user, $code);

        return response()->json([
            'success' => $sent,
            'message' => $sent ? 'New OTP sent to your email.' : 'Failed to send OTP.',
        ]);
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }

    // ─── Send OTP Email via PHPMailer ─────────────────────────────────────────

    private function sendOtpEmail(User $user, string $code): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = config('mail.mailers.smtp.host', env('MAIL_HOST'));
            $mail->SMTPAuth   = true;
            $mail->Username   = config('mail.mailers.smtp.username', env('MAIL_USERNAME'));
            $mail->Password   = config('mail.mailers.smtp.password', env('MAIL_PASSWORD'));
            $mail->SMTPSecure = config('mail.mailers.smtp.encryption', env('MAIL_ENCRYPTION', 'tls'));
            $mail->Port       = config('mail.mailers.smtp.port', env('MAIL_PORT', 587));

            $mail->setFrom(
                env('MAIL_FROM_ADDRESS', 'noreply@deped.gov.ph'),
                env('MAIL_FROM_NAME', 'DepEd 201 System')
            );
            $mail->addAddress($user->email, $user->name);

            $mail->isHTML(true);
            $mail->Subject = "{$code} is your DepEd 201 System OTP Code";
            $mail->Body    = $this->buildOtpEmailHtml($user->name, $code);
            $mail->AltBody = "Hello {$user->name},\n\nYour OTP code is: {$code}\n\nThis code expires in 10 minutes.\n\nDo not share this code with anyone.";

            $mail->send();
            return true;
        } catch (MailException $e) {
            \Log::error('PHPMailer error: ' . $mail->ErrorInfo);
            return false;
        }
    }

    // ─── Email HTML Template ──────────────────────────────────────────────────

    private function buildOtpEmailHtml(string $name, string $code): string
    {
        $firstName = explode(' ', $name)[0];
        $currentDate = now()->format('Y');
        $otpCode = $code;

        $digitBoxes = "";
        foreach (str_split($otpCode) as $d) {
            $digitBoxes .= "
            <td style=\"
                width: 50px;
                height: 60px;
                background: #ffffff;
                border: 2px solid #4f46e5;
                font-size: 28px;
                font-weight: 800;
                color: #2e3192;
                text-align: center;
                vertical-align: middle;
                border-radius: 12px;
                padding: 0;
            \">{$d}</td>
            <td width=\"8\"></td>";
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:20px;font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;background-color:#f4f7ff;color:#334155;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f7ff;">
  <tr>
    <td align="center">
      <table width="100%" border="0" cellpadding="0" cellspacing="0" style="max-width:550px;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 10px 30px rgba(79, 70, 229, 0.1);">
        
        <!-- Color Header -->
        <tr>
          <td style="background:linear-gradient(135deg, #2e3192 0%, #4f46e5 100%);padding:30px 40px;text-align:center;">
            <div style="font-size:26px;font-weight:900;color:#ffffff;letter-spacing:-0.5px;margin-bottom:4px;">DepEd 201 System</div>
            <div style="font-size:12px;color:rgba(255,255,255,0.8);text-transform:uppercase;letter-spacing:2px;">Official Authentication Service</div>
          </td>
        </tr>

        <!-- Main Body -->
        <tr>
          <td style="padding:40px;">
            <p style="font-size:16px;line-height:1.6;color:#334155;margin:0 0 25px;">
              Your 201 System OTP is ready. Use the code below to complete your login. Thank you for visiting DepEd 201 System Portal. Do not share it with anyone.
            </p>

            <div style="text-align:center;margin:35px 0;">
              <div style="display:inline-block;padding:12px 24px;background:#f1f5f9;border:1px solid #cbd5e1;border-radius:12px;font-family:'Courier New', Courier, monospace;font-size:24px;font-weight:700;color:#2e3192;letter-spacing:4px;">
                {$otpCode}
              </div>
              <p style="font-size:12px;color:#94a3b8;margin-top:10px;">(Copy and paste this code into the verification form)</p>
            </div>

            <div style="text-align:center;margin-bottom:30px;">
              <p style="font-size:13px;color:#ef4444;font-weight:600;margin:0;">Expires in 10 minutes</p>
            </div>

            <!-- Security Advisory -->
            <div style="padding:20px;background:#fff9f9;border-left:4px solid #ef4444;border-radius:10px;">
              <p style="font-size:13px;color:#991b1b;margin:0;line-height:1.5;">
                <strong>⚠️ Security Advisory:</strong> This code is confidential. Never share your OTP with anyone. If you didn't request this, please secure your account immediately.
              </p>
            </div>
          </td>
        </tr>

        <!-- Elegant Footer -->
        <tr>
          <td style="padding:30px 40px;background:#f8fafc;text-align:center;border-top:1px solid #f1f5f9;">
            <p style="font-size:12px;color:#94a3b8;margin:0 0 8px;">
              &copy; {$currentDate} Department of Education • IT Division
            </p>
            <p style="font-size:11px;color:#cbd5e1;margin:0;">
              This is an automated system message. Do not reply.
            </p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

</body>
</html>
HTML;
    }
}
