<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin OTP Verification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #333;">Hello Admin,</h2>
        <p style="font-size: 16px; color: #555;">
            Your One-Time Password (OTP) for admin verification is:
        </p>
        <h1 style="font-size: 36px; color: #007bff; text-align: center;">{{ $otp }}</h1>
        <p style="font-size: 14px; color: #999;">This code will expire in 5 minutes. Please do not share this code with anyone.</p>
        <p style="margin-top: 20px; color: #777;">Thank you,<br><strong>Seller Sathi Team</strong></p>
    </div>
</body>
</html>
