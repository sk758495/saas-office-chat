<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index()
    {
        $company = auth('admin')->user()->company;
        $designations = $company->designations()->with('department')->latest()->get();
        return view('admin.designations.index', compact('designations'));
    }

    public function create()
    {
        $company = auth('admin')->user()->company;
        $departments = $company->departments()->where('status', true)->get();
        return view('admin.designations.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        Designation::create(array_merge($request->all(), ['company_id' => auth('admin')->user()->company_id]));
        return redirect()->route('admin.designations.index')->with('success', 'Designation created successfully');
    }

    public function edit(Designation $designation)
    {
        $departments = Department::where('status', true)->get();
        return view('admin.designations.edit', compact('designation', 'departments'));
    }

    public function update(Request $request, Designation $designation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        $designation->update($request->all());
        return redirect()->route('admin.designations.index')->with('success', 'Designation updated successfully');
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();
        return redirect()->route('admin.designations.index')->with('success', 'Designation deleted successfully');
    }

    public function getByDepartment($departmentId)
    {
        $designations = Designation::where('department_id', $departmentId)
                                  ->where('status', true)
                                  ->get();
        return response()->json($designations);
    }
}