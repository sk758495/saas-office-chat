<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Designation;

class DepartmentDesignationSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'IT' => ['Software Developer', 'System Administrator', 'DevOps Engineer', 'UI/UX Designer'],
            'HR' => ['HR Manager', 'Recruiter', 'HR Executive', 'Training Coordinator'],
            'Finance' => ['Accountant', 'Financial Analyst', 'Finance Manager', 'Auditor'],
            'Marketing' => ['Marketing Manager', 'Digital Marketing Executive', 'Content Writer', 'SEO Specialist'],
            'Sales' => ['Sales Manager', 'Sales Executive', 'Business Development', 'Account Manager'],
        ];

        foreach ($departments as $deptName => $designations) {
            $department = Department::create([
                'name' => $deptName,
                'status' => true
            ]);

            foreach ($designations as $designation) {
                Designation::create([
                    'name' => $designation,
                    'department_id' => $department->id,
                    'status' => true
                ]);
            }
        }
    }
}