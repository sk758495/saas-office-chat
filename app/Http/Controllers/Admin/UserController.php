<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function store(Request $request)
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
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);
        
        return redirect()->route('admin.users.index')->with('success', 'Employee registered successfully!');
    }

    public function edit(User $user)
    {
        $company = auth('admin')->user()->company;
        
        if ($user->company_id !== $company->id) {
            abort(403);
        }
        
        $departments = $company->departments()->where('status', true)->get();
        
        return view('admin.users.edit', compact('user', 'departments', 'company'));
    }

    public function update(Request $request, User $user)
    {
        $company = auth('admin')->user()->company;
        
        if ($user->company_id !== $company->id) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'required|string|max:15',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'password' => 'nullable|min:8|confirmed',
        ]);
        
        $data = $request->only(['name', 'email', 'mobile', 'department_id', 'designation_id']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);
        
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        $company = auth('admin')->user()->company;
        
        if ($user->company_id !== $company->id) {
            abort(403);
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    public function toggleStatus(User $user)
    {
        $company = auth('admin')->user()->company;
        
        if ($user->company_id !== $company->id) {
            abort(403);
        }
        
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "User {$status} successfully!");
    }
}