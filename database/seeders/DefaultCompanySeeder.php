<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class DefaultCompanySeeder extends Seeder
{
    public function run(): void
    {
        // Create default company
        $company = Company::create([
            'name' => 'Default Company',
            'email' => 'admin@defaultcompany.com',
            'phone' => '+1234567890',
            'address' => '123 Default Street, Default City',
            'plan' => 'paid',
            'is_active' => true,
            'max_users' => 999999,
            'max_storage_mb' => 999999,
            'subscription_expires_at' => now()->addYear(),
            'subscription_amount' => 999.99,
        ]);

        // Create super admin
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@system.com',
            'password' => Hash::make('password123'),
            'company_id' => $company->id,
            'role' => 'super_admin'
        ]);

        // Create company admin
        Admin::create([
            'name' => 'Company Admin',
            'email' => 'admin@defaultcompany.com',
            'password' => Hash::make('password123'),
            'company_id' => $company->id,
            'role' => 'company_admin'
        ]);
    }
}