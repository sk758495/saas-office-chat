<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Office Chat</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 28px;">🎉 Congratulations!</h1>
            <p style="color: #e2e8f0; margin: 10px 0 0 0; font-size: 16px;">Your company is now verified and ready to go!</p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <h2 style="color: #2d3748; margin-top: 0;">Welcome {{ $company->name }}!</h2>
            
            <p>Thank you for choosing Office Chat for your team communication needs. Your company account has been successfully verified and activated.</p>

            <!-- Quick Start Guide -->
            <div style="background: #f7fafc; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #4299e1;">
                <h3 style="color: #2d3748; margin-top: 0;">🚀 Quick Start Guide</h3>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Login to your admin dashboard</li>
                    <li>Create departments and designations</li>
                    <li>Add team members to your company</li>
                    <li>Start chatting and collaborating!</li>
                </ul>
            </div>

            <!-- Current Plan Info -->
            <div style="background: #edf2f7; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #2d3748; margin-top: 0;">📋 Your Current Plan: {{ ucfirst($company->plan) }}</h3>
                <p style="margin: 10px 0;">
                    <strong>Max Users:</strong> {{ $company->max_users }}<br>
                    <strong>Storage:</strong> {{ $company->max_storage_mb }}MB
                </p>
            </div>

            @if($company->plan === 'free')
            <!-- Premium Upgrade Promotion -->
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 25px; border-radius: 8px; margin: 20px 0; text-align: center;">
                <h3 style="color: white; margin-top: 0;">🌟 Upgrade to Premium</h3>
                <p style="color: #fed7d7; margin: 15px 0;">Unlock unlimited users, storage, and advanced features!</p>
                <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 6px; margin: 15px 0;">
                    <p style="color: white; margin: 0; font-size: 18px;"><strong>Only $999.99/year</strong></p>
                </div>
                <a href="{{ url('/company/dashboard') }}" style="display: inline-block; background: white; color: #f5576c; padding: 12px 25px; text-decoration: none; border-radius: 25px; font-weight: bold; margin-top: 10px;">
                    Upgrade Now
                </a>
            </div>
            @endif

            <!-- Login Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('admin.login') }}" style="display: inline-block; background: #4299e1; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;">
                    Access Your Dashboard
                </a>
            </div>

            <!-- Support Info -->
            <div style="background: #f0fff4; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #48bb78;">
                <h3 style="color: #2d3748; margin-top: 0;">💬 Need Help?</h3>
                <p style="margin: 0;">Our support team is here to help you get started. Contact us anytime for assistance with setup or questions about features.</p>
            </div>

            <p style="margin-top: 30px;">We're excited to have you on board and look forward to helping your team communicate more effectively!</p>
            
            <p style="margin-bottom: 0;"><strong>Best regards,</strong><br>The Office Chat Team</p>
        </div>

        <!-- Footer -->
        <div style="background: #edf2f7; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;">
            <p style="margin: 0; font-size: 12px; color: #718096;">
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>