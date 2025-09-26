<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class NewPasswordController extends Controller
{
    /**
     * Show password reset form.
     */
    public function showResetForm()
    {
        $email = Session::get('password_reset_email');
        
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Session expired. Please request OTP again.']);
        }

        return view('auth.reset-password', ['email' => $email]);
    }

    /**
     * Handle password reset.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found']);
        }

        // Update user password
        $user->update([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ]);

        // Clear session
        Session::forget('password_reset_email');

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Password has been reset successfully!');
    }
}
