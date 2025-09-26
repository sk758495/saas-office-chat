<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
</head>
<body>
    <h2>Hello,</h2>
    <p>You have requested to reset your password. Use the following OTP to reset your password:</p>
    
    <h1 style="color: #ff6600; text-align: center;">{{ $otp }}</h1>

    <p>This OTP is valid for **10 minutes**. If you did not request a password reset, please ignore this email.</p>

    <br>
    <p>Regards,</p>
    <p><strong>Your Application Team</strong></p>
</body>
</html>
