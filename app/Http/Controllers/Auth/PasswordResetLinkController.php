<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the forgot password form.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle the OTP request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
    
        // Generate OTP
        $otp = rand(100000, 999999);
    
        // Store OTP and email in session
        Session::put('password_reset_otp', $otp);
        Session::put('password_reset_email', $request->email);
    
        // Send OTP via email
        Mail::send('emails.password-reset-otp', ['otp' => $otp], function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Your Password Reset OTP');
        });
    
        return redirect()->route('password.verify-otp');
    }
    
}

