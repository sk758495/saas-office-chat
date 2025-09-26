<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Company;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $companies = Company::where('is_active', true)->get();
        $departments = Department::where('status', true)->get();
        return view('auth.register', compact('departments', 'companies'));
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'mobile' => ['required', 'string', 'max:15'],
            'company_id' => ['required', 'exists:companies,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'designation_id' => ['required', 'exists:designations,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Check if company can add more users
        $company = Company::find($request->company_id);
        if (!$company->canAddUser()) {
            return back()->withErrors(['company_id' => 'Company has reached maximum user limit for their plan.']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'company_id' => $request->company_id,
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user); // Log in the user immediately after registration

        event(new Registered($user));

        // Generate OTP
        $otp = rand(100000, 999999); // Generate a 6-digit OTP

        // Store OTP in the session (or database if you prefer)
        session(['otp' => $otp, 'otp_expiration' => now()->addMinutes(5)]); // OTP expires in 5 minutes

        // Send OTP to user's email
        Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($otp));

        // Redirect to OTP verification page
        return redirect()->route('auth.otp.verify');
    }
}
