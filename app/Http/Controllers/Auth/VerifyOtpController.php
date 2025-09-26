<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

use Carbon\Carbon;

class VerifyOtpController extends Controller
{
    /**
     * Show OTP verification form.
     */
    public function showOtpForm()
    {
        return view('auth.reset-password-verify-otp');
    }

    /**
     * Handle OTP verification.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
        ]);
    
        // Retrieve OTP from session
        $otp = Session::get('password_reset_otp');
        $email = Session::get('password_reset_email');
    
        // Check if OTP matches the session data
        if ($otp != $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP']);
        }
    
        // Check if OTP has expired (you can set a custom expiration time, e.g., 10 minutes)
        $otpSentTime = Session::get('otp_sent_time');
        if ($otpSentTime && Carbon::parse($otpSentTime)->addMinutes(10)->isPast()) {
            // OTP expired
            return back()->withErrors(['otp' => 'OTP has expired']);
        }
    
        // OTP is valid, redirect to reset password form
        return redirect()->route('password.reset', ['email' => $request->email]);
    }
    


}
