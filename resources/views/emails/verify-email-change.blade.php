<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Your New Email Address</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f9; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2 style="color: #6366f1; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Email Verification</h2>
        <p>Hello <strong>{{ $userName }}</strong>,</p>
        <p>You have requested to change your email address to: <strong>{{ $newEmail }}</strong></p>
        <p>Please click the button below to verify this change. This link will expire in 24 hours.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('profile.verify-email', ['token' => $token]) }}" 
               style="background-color: #6366f1; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">
                Verify New Email Address
            </a>
        </div>
        
        <p style="color: #64748b; font-size: 0.9em;">If you did not request this change, please ignore this email.</p>
        <p style="color: #64748b; font-size: 0.9em;">If you're having trouble clicking the button, copy and paste the URL below into your web browser:</p>
        <p style="font-size: 0.8em; word-break: break-all;"><a href="{{ route('profile.verify-email', ['token' => $token]) }}">{{ route('profile.verify-email', ['token' => $token]) }}</a></p>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 0.8em; color: #94a3b8; text-align: center;">
            &copy; {{ date('Y') }} HRNTP 201 System. All rights reserved.
        </div>
    </div>
</body>
</html>
