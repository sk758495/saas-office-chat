<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'mobile' => 'required|string|max:15',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
        ]);

        // Send OTP for email verification
        $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'email_verification_token' => $otp,
            'email_verification_expires_at' => now()->addMinutes(10)
        ]);

        Mail::raw("Your verification OTP: {$otp}\nThis OTP will expire in 10 minutes.", function($message) use ($user) {
            $message->to($user->email)->subject('Email Verification - Office Chat');
        });

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. Please verify your email.',
            'user' => $user->load(['department', 'designation']),
            'token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials are incorrect.',
                'errors' => ['email' => ['Invalid email or password']]
            ], 401)->header('Access-Control-Allow-Origin', '*')
                   ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                   ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Update online status
        $user->update([
            'is_online' => true,
            'last_seen' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user->load(['department', 'designation']),
            'token' => $token,
            'token_type' => 'Bearer'
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    public function logout(Request $request)
    {
        // Update offline status
        $request->user()->update([
            'is_online' => false,
            'last_seen' => now()
        ]);

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $user = $request->user();

        if ($user->email_verification_token !== $request->otp || $user->email_verification_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'email_verification_expires_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully'
        ]);
    }

    public function resendOtp(Request $request)
    {
        $user = $request->user();
        
        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified'
            ], 400);
        }

        $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'email_verification_token' => $otp,
            'email_verification_expires_at' => now()->addMinutes(10)
        ]);

        Mail::raw("Your verification OTP: {$otp}\nThis OTP will expire in 10 minutes.", function($message) use ($user) {
            $message->to($user->email)->subject('Email Verification - Office Chat');
        });

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()->load(['department', 'designation'])
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();
        
        $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'password_reset_token' => $otp,
            'password_reset_expires_at' => now()->addMinutes(10)
        ]);

        Mail::raw("Your password reset OTP: {$otp}\nThis OTP will expire in 10 minutes.", function($message) use ($user) {
            $message->to($user->email)->subject('Password Reset - Office Chat');
        });

        return response()->json([
            'success' => true,
            'message' => 'Password reset OTP sent to your email'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->password_reset_token !== $request->otp || $user->password_reset_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'password_reset_token' => null,
            'password_reset_expires_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);
    }
}