<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

class SendOtpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-otp {userId} {code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends OTP email via PHPMailer in the background';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $code = $this->argument('code');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID $userId not found.");
            return 1;
        }

        $this->info("Attempting to send OTP to: " . $user->email);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host', env('MAIL_HOST', 'smtp.gmail.com'));
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.mailers.smtp.username', env('MAIL_USERNAME'));
            $mail->Password = config('mail.mailers.smtp.password', env('MAIL_PASSWORD'));
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
            $mail->Port       = config('mail.mailers.smtp.port', env('MAIL_PORT', 587));
            $mail->Timeout    = 60;
            $mail->SMTPKeepAlive = false;

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
            $this->info("Email sent successfully!");
            \Log::info("Background OTP Send SUCCESS for user {$userId} and email {$user->email}");
            return 0;
        } catch (MailException $e) {
            $this->error("PHPMailer error: " . $mail->ErrorInfo);
            \Log::error("Background OTP PHPMailer Error: " . $mail->ErrorInfo);
            return 1;
        } catch (\Exception $e) {
            $this->error("General email error: " . $e->getMessage());
            \Log::error("Background OTP General Error: " . $e->getMessage());
            return 1;
        }
    }

    private function buildOtpEmailHtml($name, $code)
    {
        $currentDate = date('Y');
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
}
