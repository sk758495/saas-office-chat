<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    public function show()
    {
        return response()->json([
            'success' => true,
            'user' => Auth::user()->load(['department', 'designation'])
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|max:2048'
        ]);

        $user = Auth::user();
        
        if ($request->hasFile('profile_photo')) {
            $fileName = time() . '_' . $request->file('profile_photo')->getClientOriginalName();
            $filePath = $request->file('profile_photo')->storeAs('profile-photos', $fileName, 'public');
            $user->update(['profile_photo' => $filePath]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile photo updated successfully',
            'photo_url' => '/storage/' . $filePath
        ]);
    }

    public function updatePin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4',
            'confirm_pin' => 'required|same:pin'
        ]);

        $user = Auth::user();
        $isNewPin = !$user->chat_pin;
        
        $user->update([
            'chat_pin' => $request->pin,
            'chat_lock_enabled' => true
        ]);

        $message = $isNewPin ? 'PIN set successfully!' : 'PIN updated successfully!';
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function toggleLock(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->chat_pin && !$user->chat_lock_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Please set a PIN first'
            ], 400);
        }
        
        $newStatus = !$user->chat_lock_enabled;
        $user->update(['chat_lock_enabled' => $newStatus]);
        
        $message = $newStatus ? 'Chat lock enabled' : 'Chat lock disabled';
        
        return response()->json([
            'success' => true,
            'enabled' => $newStatus,
            'message' => $message
        ]);
    }

    public function verifyPin(Request $request)
    {
        $request->validate(['pin' => 'required|digits:4']);
        
        $user = Auth::user();
        
        if ($request->pin === $user->chat_pin) {
            return response()->json([
                'success' => true,
                'message' => 'PIN verified successfully'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Incorrect PIN'
        ], 400);
    }

    public function forgotPin(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $user = Auth::user();
        
        if ($user->email !== $request->email) {
            return response()->json([
                'success' => false,
                'message' => 'Email does not match'
            ], 400);
        }
        
        $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->pin_reset_token = $otp;
        $user->pin_reset_expires_at = now()->addMinutes(10);
        $user->save();
        
        Mail::raw("Your PIN reset OTP: {$otp}\nThis OTP will expire in 10 minutes.", function($message) use ($user) {
            $message->to($user->email)->subject('PIN Reset OTP - Office Chat');
        });
        
        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);
        
        $user = Auth::user();
        $user->refresh();
        
        $storedOtp = trim((string) $user->pin_reset_token);
        $inputOtp = trim((string) $request->otp);
        
        if ($storedOtp !== $inputOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }
        
        if ($user->pin_reset_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired'
            ], 400);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully'
        ]);
    }

    public function resetPinAfterOtp(Request $request)
    {
        $request->validate([
            'new_pin' => 'required|digits:4',
            'confirm_pin' => 'required|same:new_pin',
            'otp' => 'required|digits:6'
        ]);
        
        $user = Auth::user();
        $user->refresh();
        
        // Verify OTP again for security
        if ($user->pin_reset_token !== $request->otp || $user->pin_reset_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }
        
        $user->update([
            'chat_pin' => $request->new_pin,
            'pin_reset_token' => null,
            'pin_reset_expires_at' => null,
            'chat_lock_enabled' => true
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'PIN reset successfully'
        ]);
    }

    public function removePin(Request $request)
    {
        $user = Auth::user();
        $user->update([
            'chat_pin' => null,
            'chat_lock_enabled' => false
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'PIN removed successfully'
        ]);
    }

    public function sendCurrentEmailVerification(Request $request)
    {
        $user = Auth::user();
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
            'message' => 'Verification OTP sent to your current email'
        ]);
    }

    public function verifyCurrentEmail(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);
        
        $user = Auth::user();
        
        if ($user->email_verification_token !== $request->otp || $user->email_verification_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully',
            'verification_token' => $user->email_verification_token // Return for next step
        ]);
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'current_email_otp' => 'required|digits:6'
        ]);

        $user = Auth::user();
        
        // Verify current email OTP
        if ($user->email_verification_token !== $request->current_email_otp || $user->email_verification_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your current email first'
            ], 400);
        }
        
        $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store new email data temporarily
        $user->update([
            'new_email' => $request->email,
            'new_email_otp' => $otp,
            'new_email_expires_at' => now()->addMinutes(10)
        ]);
        
        Mail::raw("Your new email verification OTP: {$otp}\nThis OTP will expire in 10 minutes.", function($message) use ($request) {
            $message->to($request->email)->subject('New Email Verification - Office Chat');
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Verification OTP sent to your new email'
        ]);
    }

    public function verifyNewEmail(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);
        
        $user = Auth::user();
        
        if (!$user->new_email || $user->new_email_otp !== $request->otp || $user->new_email_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }
        
        $user->update([
            'email' => $user->new_email,
            'new_email' => null,
            'new_email_otp' => null,
            'new_email_expires_at' => null,
            'email_verification_token' => null,
            'email_verification_expires_at' => null
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Email updated successfully'
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
            'current_email_otp' => 'required|digits:6'
        ]);

        $user = Auth::user();
        
        // Verify current email OTP
        if ($user->email_verification_token !== $request->current_email_otp || $user->email_verification_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email first'
            ], 400);
        }
        
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'email_verification_token' => null,
            'email_verification_expires_at' => null
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    }
}