<?php
// Simple test authentication for call testing
// Add this to your routes/web.php for testing purposes only

use Illuminate\Support\Facades\Route;
use App\Models\User;

// Test route that simulates authentication for call testing
Route::get('/test-call-auth', function() {
    // Create or get a test user
    $testUser = User::firstOrCreate(
        ['email' => 'test@emplora.com'],
        [
            'name' => 'Test User',
            'password' => bcrypt('password'),
            'company_id' => 1,
            'department_id' => 1,
            'designation_id' => 1,
            'is_verified' => true
        ]
    );
    
    // Log in the test user
    auth()->login($testUser);
    
    return response()->json([
        'success' => true,
        'user' => $testUser,
        'message' => 'Test user authenticated'
    ]);
});

// Test call page with authentication
Route::get('/call-test-auth', function() {
    // Create or get a test user
    $testUser = User::firstOrCreate(
        ['email' => 'test@emplora.com'],
        [
            'name' => 'Test User',
            'password' => bcrypt('password'),
            'company_id' => 1,
            'department_id' => 1,
            'designation_id' => 1,
            'is_verified' => true
        ]
    );
    
    // Log in the test user
    auth()->login($testUser);
    
    // Return the test page with proper authentication
    return view('call-test', ['user' => $testUser]);
});