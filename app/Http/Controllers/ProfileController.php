<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => Auth::user()]);
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

        return response()->json(['success' => true, 'photo_url' => '/storage/' . $filePath]);
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
        return response()->json(['success' => true, 'message' => $message]);
    }

    public function toggleLock(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->chat_pin && !$user->chat_lock_enabled) {
            return response()->json(['error' => 'Please set a PIN first'], 400);
        }
        
        $newStatus = !$user->chat_lock_enabled;
        $user->update(['chat_lock_enabled' => $newStatus]);
        
        $message = $newStatus ? 'Chat lock enabled' : 'Chat lock disabled';
        return response()->json(['enabled' => $newStatus, 'message' => $message]);
    }

    public function verifyPin(Request $request)
    {
        $request->validate(['pin' => 'required|digits:4']);
        
        $user = Auth::user();
        
        if ($request->pin === $user->chat_pin) {
            session(['chat_unlocked' => true]);
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Incorrect PIN'], 400);
    }

    public function forgotPin(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $user = Auth::user();
        
        if ($user->email !== $request->email) {
            return response()->json(['error' => 'Email does not match'], 400);
        }
        
        $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        // Force save to database
        $user->pin_reset_token = $otp;
        $user->pin_reset_expires_at = now()->addMinutes(10);
        $user->save();
        
        // Verify it was saved
        $user->refresh();
        \Log::info('OTP Generated', ['otp' => $otp, 'saved_otp' => $user->pin_reset_token]);
        
        Mail::raw("Your PIN reset OTP: {$otp}\nThis OTP will expire in 10 minutes.", function($message) use ($user) {
            $message->to($user->email)->subject('PIN Reset OTP - Office Chat');
        });
        
        return response()->json(['success' => true, 'message' => 'OTP sent to your email']);
    }

    public function resetPin($token)
    {
        $user = Auth::user();
        
        if ($user->pin_reset_token !== $token || $user->pin_reset_expires_at < now()) {
            return redirect()->route('chat.index')->with('error', 'Invalid or expired token');
        }
        
        return view('profile.reset-pin', compact('token'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);
        
        $user = Auth::user();
        
        // Refresh user data from database
        $user->refresh();
        
        $storedOtp = trim((string) $user->pin_reset_token);
        $inputOtp = trim((string) $request->otp);
        
        // Debug - remove after testing
        \Log::info('OTP Debug', [
            'stored' => $storedOtp,
            'input' => $inputOtp,
            'stored_length' => strlen($storedOtp),
            'input_length' => strlen($inputOtp),
            'expires_at' => $user->pin_reset_expires_at,
            'now' => now()
        ]);
        
        if ($storedOtp !== $inputOtp) {
            return response()->json(['error' => "Invalid OTP. Expected: {$storedOtp}, Got: {$inputOtp}"], 400);
        }
        
        if ($user->pin_reset_expires_at < now()) {
            return response()->json(['error' => 'OTP has expired'], 400);
        }
        
        session(['otp_verified' => true, 'otp_user_id' => $user->id]);
        return response()->json(['success' => true, 'message' => 'OTP verified successfully!']);
    }

    public function resetPinAfterOtp(Request $request)
    {
        $request->validate([
            'new_pin' => 'required|digits:4',
            'confirm_pin' => 'required|same:new_pin'
        ]);
        
        if (!session('otp_verified') || session('otp_user_id') != Auth::id()) {
            return response()->json(['error' => 'OTP not verified'], 400);
        }
        
        $user = Auth::user();
        $user->update([
            'chat_pin' => $request->new_pin,
            'pin_reset_token' => null,
            'pin_reset_expires_at' => null,
            'chat_lock_enabled' => true
        ]);
        
        session()->forget(['otp_verified', 'otp_user_id', 'chat_unlocked']);
        return response()->json(['success' => true, 'message' => 'PIN reset successfully!']);
    }

    public function removePin(Request $request)
    {
        $user = Auth::user();
        $user->update([
            'chat_pin' => null,
            'chat_lock_enabled' => false
        ]);
        
        session()->forget('chat_unlocked');
        return response()->json(['success' => true, 'message' => 'PIN removed successfully!']);
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
        
        return response()->json(['success' => true, 'message' => 'Verification OTP sent to your current email']);
    }

    public function verifyCurrentEmail(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);
        
        $user = Auth::user();
        
        if ($user->email_verification_token !== $request->otp || $user->email_verification_expires_at < now()) {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }
        
        session(['current_email_verified' => true, 'verified_user_id' => $user->id]);
        return response()->json(['success' => true, 'message' => 'Email verified successfully!']);
    }

    public function updateEmail(Request $request)
    {
        if (!session('current_email_verified') || session('verified_user_id') != Auth::id()) {
            return response()->json(['error' => 'Please verify your current email first'], 400);
        }
        
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id()
        ]);

        $user = Auth::user();
        $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        session([
            'new_email' => $request->email,
            'new_email_otp' => $otp,
            'new_email_expires_at' => now()->addMinutes(10)
        ]);
        
        Mail::raw("Your new email verification OTP: {$otp}\nThis OTP will expire in 10 minutes.", function($message) use ($request) {
            $message->to($request->email)->subject('New Email Verification - Office Chat');
        });
        
        return response()->json(['success' => true, 'message' => 'Verification OTP sent to your new email']);
    }

    public function verifyNewEmail(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);
        
        if (!session('new_email') || session('new_email_otp') !== $request->otp || session('new_email_expires_at') < now()) {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }
        
        Auth::user()->update(['email' => session('new_email')]);
        
        session()->forget(['current_email_verified', 'verified_user_id', 'new_email', 'new_email_otp', 'new_email_expires_at']);
        return response()->json(['success' => true, 'message' => 'Email updated successfully!']);
    }

    public function updatePassword(Request $request)
    {
        if (!session('current_email_verified') || session('verified_user_id') != Auth::id()) {
            return response()->json(['error' => 'Please verify your email first'], 400);
        }
        
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 400);
        }

        $user->update(['password' => Hash::make($request->password)]);
        session()->forget(['current_email_verified', 'verified_user_id']);
        return response()->json(['success' => true, 'message' => 'Password updated successfully!']);
    }
}