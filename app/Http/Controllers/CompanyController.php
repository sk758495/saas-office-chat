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
            'company_email' => 'required|email|unique:companies,email',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:admins,email',
            'admin_password' => 'required|string|min:8|confirmed',
            'plan' => 'required|in:free,paid',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('company-logos', 'public');
        }

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
        ]);

        Admin::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'company_id' => $company->id,
            'role' => 'company_admin'
        ]);

        return redirect()->route('admin.login')->with('success', 'Company registered successfully! Please login.');
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
}