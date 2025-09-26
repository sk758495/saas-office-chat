<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;


class OtpVerificationController extends Controller
{
    /**
     * Display OTP verification view.
     */
    public function show(): View
    {
        return view('auth.verify-otp');
    }

    /**
     * Handle OTP verification.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        // Check if OTP exists and is valid
        if ($request->otp == session('otp') && now()->lessThan(session('otp_expiration'))) {
            // OTP is valid
            // Mark the email as verified or handle success
            $user = $request->user();
            $user->email_verified_at = now();
            $user->save();

            session()->forget(['otp', 'otp_expiration']); // Clear OTP session data

            return redirect()->route('dashboard');
        }

        // OTP is invalid or expired
        return back()->withErrors(['otp' => 'The OTP is invalid or has expired.']);
    }

    public function resend(Request $request): RedirectResponse
    {
        // Ensure user is authenticated
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'You need to be logged in to resend OTP']);
        }

        // Generate a new OTP
        $otp = rand(100000, 999999); // Generate a 6-digit OTP

        // Store OTP in the session (or database if you prefer)
        session(['otp' => $otp, 'otp_expiration' => now()->addMinutes(5)]); // OTP expires in 5 minutes

        // Send OTP to user's email
        Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($otp));

        // Return response
        return back()->with('status', 'A new OTP has been sent to your email!');
    }
}
