<!DOCTYPE html>
<html>
<head>
    <title>Company Email Verification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2563eb;">Company Email Verification</h2>
        
        <p>Hello {{ $companyName }},</p>
        
        <p>Thank you for registering your company with our platform. To complete your registration, please verify your company email address.</p>
        
        <div style="background: #f3f4f6; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0;">
            <h3 style="margin: 0; color: #1f2937;">Your Verification Code</h3>
            <div style="font-size: 32px; font-weight: bold; color: #2563eb; margin: 10px 0; letter-spacing: 5px;">
                {{ $otp }}
            </div>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">This code expires in 10 minutes</p>
        </div>
        
        <p>Please enter this code on the verification page to activate your company account.</p>
        
        <p style="color: #dc2626; font-size: 14px;">
            <strong>Important:</strong> If you did not register a company account, please ignore this email.
        </p>
        
        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
        
        <p style="font-size: 12px; color: #6b7280;">
            This is an automated email. Please do not reply to this message.
        </p>
    </div>
</body>
</html>