<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $company = auth('admin')->user()->company;
        $users = $company->users()->with(['department', 'designation'])->paginate(15);
        
        return view('admin.users.index', compact('users', 'company'));
    }

    public function create()
    {
        $company = auth('admin')->user()->company;
        $departments = $company->departments()->where('status', true)->get();
        
        return view('admin.users.create', compact('departments', 'company'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $company = auth('admin')->user()->company;
        
        if (!$company->canAddUser()) {
            return back()->withErrors(['error' => 'Company has reached maximum user limit.']);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|string|max:15',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'password' => 'required|min:8|confirmed',
        ]);
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'company_id' => $company->id,
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'email_verified_at' => now(),
        ]);
        
        return redirect()->route('admin.users.index')->with('success', 'Employee registered successfully!');
    }
}