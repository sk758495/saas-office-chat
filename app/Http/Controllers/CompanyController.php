<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function register()
    {
        return view('company.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => [
                'required',
                'email',
                'unique:companies,email',
                function ($attribute, $value, $fail) {
                    $blockedDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'aol.com', 'icloud.com', 'protonmail.com'];
                    $domain = substr(strrchr($value, '@'), 1);
                    if (in_array(strtolower($domain), $blockedDomains)) {
                        $fail('Please use a company email address, not a personal email.');
                    }
                }
            ],
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string',
            'admin_name' => 'required|string|max:255',
            'admin_email' => [
                'required',
                'email',
                'unique:admins,email',
                function ($attribute, $value, $fail) {
                    $blockedDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'aol.com', 'icloud.com', 'protonmail.com'];
                    $domain = substr(strrchr($value, '@'), 1);
                    if (in_array(strtolower($domain), $blockedDomains)) {
                        $fail('Please use a company email address, not a personal email.');
                    }
                }
            ],
            'admin_password' => 'required|string|min:8|confirmed',
            'plan' => 'required|in:free,paid',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('company-logos', 'public');
        }

        $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        $company = Company::create([
            'name' => $request->company_name,
            'email' => $request->company_email,
            'phone' => $request->company_phone,
            'address' => $request->company_address,
            'logo' => $logoPath,
            'plan' => $request->plan,
            'max_users' => $request->plan === 'paid' ? 999999 : 5,
            'max_storage_mb' => $request->plan === 'paid' ? 999999 : 100,
            'subscription_expires_at' => $request->plan === 'paid' ? now()->addYear() : null,
            'subscription_amount' => $request->plan === 'paid' ? 999.99 : 0,
            'email_verification_token' => $otp,
            'email_verification_expires_at' => now()->addMinutes(10),
            'is_verified' => false,
        ]);

        Admin::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'company_id' => $company->id,
            'role' => 'company_admin'
        ]);

        \Illuminate\Support\Facades\Mail::to($company->email)->send(new \App\Mail\CompanyOtpMail($otp, $company->name));

        session(['company_id' => $company->id]);
        return redirect()->route('company.verify-email')->with('success', 'Please check your company email for verification code.');
    }

    public function dashboard()
    {
        $company = auth('admin')->user()->company;
        $stats = [
            'total_users' => $company->users()->count(),
            'active_users' => $company->users()->where('is_online', true)->count(),
            'total_groups' => $company->groups()->count(),
            'total_departments' => $company->departments()->count(),
        ];

        return view('company.dashboard', compact('company', 'stats'));
    }

    public function settings()
    {
        $company = auth('admin')->user()->company;
        return view('company.settings', compact('company'));
    }

    public function updateSettings(Request $request)
    {
        $company = auth('admin')->user()->company;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only(['name', 'email', 'phone', 'address']);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->file('logo')->store('company-logos', 'public');
        }

        $company->update($data);

        return back()->with('success', 'Company settings updated successfully!');
    }

    public function upgrade()
    {
        $company = auth('admin')->user()->company;
        
        if ($company->plan === 'free') {
            $company->update([
                'plan' => 'paid',
                'max_users' => 999999,
                'max_storage_mb' => 999999,
                'subscription_expires_at' => now()->addYear(),
                'subscription_amount' => 999.99,
            ]);

            return back()->with('success', 'Successfully upgraded to paid plan!');
        }

        return back()->with('error', 'Company is already on paid plan.');
    }

    public function updateChatTheme(Request $request)
    {
        $request->validate([
            'chat_primary_color' => 'required|string|max:7',
            'chat_secondary_color' => 'required|string|max:7',
            'chat_theme_name' => 'required|string|max:50'
        ]);

        $company = auth('admin')->user()->company;
        $company->update([
            'chat_primary_color' => $request->chat_primary_color,
            'chat_secondary_color' => $request->chat_secondary_color,
            'chat_theme_name' => $request->chat_theme_name
        ]);

        return back()->with('success', 'Chat theme updated successfully!');
    }

    public function showVerifyEmail()
    {
        $companyId = session('company_id');
        
        // If no session, try to find an unverified company
        if (!$companyId) {
            $company = Company::where('is_verified', false)
                            ->whereNotNull('email_verification_token')
                            ->latest()
                            ->first();
            
            if ($company) {
                session(['company_id' => $company->id]);
                $companyId = $company->id;
            } else {
                return redirect()->route('company.register');
            }
        }
        
        $company = Company::find($companyId);
        if (!$company || $company->is_verified) {
            return redirect()->route('admin.login');
        }
        
        return view('company.verify-email', compact('company'));
    }

    public function verifyEmail(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);
        
        $companyId = session('company_id');
        $company = Company::find($companyId);
        
        if (!$company) {
            return back()->withErrors(['otp' => 'Invalid session. Please register again.']);
        }
        
        // Debug: Check expiration time
        if ($company->email_verification_expires_at) {
            $now = now();
            $expires = $company->email_verification_expires_at;
            
            if ($expires->isPast()) {
                return back()->withErrors(['otp' => 'OTP expired at ' . $expires->format('H:i:s') . '. Current time: ' . $now->format('H:i:s')]);
            }
        }
        
        $submittedOtp = trim($request->otp);
        $storedOtp = trim($company->email_verification_token);
        
        // Debug info (remove in production)
        \Log::info('OTP Verification Debug', [
            'submitted' => $submittedOtp,
            'stored' => $storedOtp,
            'company_id' => $company->id
        ]);
        
        if ($submittedOtp !== $storedOtp) {
            return back()->withErrors(['otp' => 'Invalid OTP code. Submitted: ' . $submittedOtp . ', Expected: ' . $storedOtp]);
        }
        
        $company->update([
            'is_verified' => true,
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'email_verification_expires_at' => null,
        ]);
        
        // Send welcome email
        \Illuminate\Support\Facades\Mail::to($company->email)->send(new \App\Mail\CompanyWelcomeMail($company));
        
        session()->forget('company_id');
        return redirect()->route('admin.login')->with('success', 'Company verified successfully! Check your email for next steps.');
    }

    public function resendOtp()
    {
        $companyId = session('company_id');
        $company = Company::find($companyId);
        
        if (!$company) {
            return back()->withErrors(['error' => 'Invalid session.']);
        }
        
        $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        $company->update([
            'email_verification_token' => $otp,
            'email_verification_expires_at' => now()->addMinutes(10),
        ]);
        
        \Illuminate\Support\Facades\Mail::to($company->email)->send(new \App\Mail\CompanyOtpMail($otp, $company->name));
        
        return back()->with('success', 'New OTP sent to your company email.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = auth('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $admin->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}