<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class AdminAuthController extends Controller
{

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function showRegisterForm()
    {
        return view('admin.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins',
            'mobile' => ['required', 'string', 'max:15'],
            'password' => 'required|confirmed|min:6',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('admin')->login($admin);

        // OTP setup
        $otp = rand(100000, 999999);
        session(['admin_otp' => $otp, 'admin_otp_expire' => now()->addMinutes(5)]);
        Mail::to($admin->email)->send(new \App\Mail\AdminOtpMail($otp));

        return redirect()->route('admin.otp.form');
    }

    public function showOtpForm()
    {
        return view('admin.auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required']);

        if ($request->otp == session('admin_otp') && now()->lt(session('admin_otp_expire'))) {
            return redirect()->route('company.dashboard');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $admin = Auth::guard('admin')->user();
            if (!$admin->company->is_verified) {
                Auth::guard('admin')->logout();
                return back()->withErrors(['email' => 'Company email not verified. Please complete email verification first.']);
            }
            return redirect()->route('company.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            return back()->withErrors(['email' => 'Email not found.']);
        }

        $otp = rand(100000, 999999);
        $admin->update([
            'password_reset_otp' => $otp,
            'password_reset_expires_at' => now()->addMinutes(10)
        ]);

        Mail::to($admin->email)->send(new \App\Mail\AdminOtpMail($otp, 'Password Reset'));
        
        return redirect()->route('admin.reset-password')->with('email', $request->email)
                        ->with('success', 'OTP sent to your email.');
    }

    public function showResetPasswordForm()
    {
        return view('admin.auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|confirmed|min:6'
        ]);

        $admin = Admin::where('email', $request->email)
                     ->where('password_reset_otp', $request->otp)
                     ->where('password_reset_expires_at', '>', now())
                     ->first();

        if (!$admin) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $admin->update([
            'password' => Hash::make($request->password),
            'password_reset_otp' => null,
            'password_reset_expires_at' => null
        ]);

        return redirect()->route('admin.login')->with('success', 'Password reset successfully.');
    }

    public function showLoginOtpForm()
    {
        return view('admin.auth.login-otp');
    }

    public function sendLoginOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            return back()->withErrors(['email' => 'Email not found.']);
        }

        $otp = rand(100000, 999999);
        session([
            'login_otp' => $otp,
            'login_otp_email' => $request->email,
            'login_otp_expires' => now()->addMinutes(5)
        ]);

        Mail::to($admin->email)->send(new \App\Mail\AdminOtpMail($otp, 'Login Verification'));
        
        return back()->with('otp_sent', true)->with('success', 'OTP sent to your email.');
    }

    public function verifyLoginOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        if ($request->otp == session('login_otp') && 
            now()->lt(session('login_otp_expires'))) {
            
            $admin = Admin::where('email', session('login_otp_email'))->first();
            Auth::guard('admin')->login($admin);
            
            session()->forget(['login_otp', 'login_otp_email', 'login_otp_expires']);
            
            return redirect()->route('company.dashboard');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }

    public function resendOtp(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $otp = rand(100000, 999999);
        session(['admin_otp' => $otp, 'admin_otp_expire' => now()->addMinutes(5)]);
        Mail::to($admin->email)->send(new \App\Mail\AdminOtpMail($otp));
        
        return back()->with('success', 'OTP resent successfully.');
    }
}
