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
}
