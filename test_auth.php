<?php

// Simple test script to debug authentication
// Run with: php test_auth.php

$baseUrl = 'http://127.0.0.1:8000/api';

// Test 1: Login to get fresh token
echo "=== Testing Login ===\n";
$loginData = [
    'email' => 'admin@example.com', // Change to your test user
    'password' => 'password'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login Response Code: $httpCode\n";
echo "Login Response: $response\n\n";

$loginResult = json_decode($response, true);

if (isset($loginResult['token'])) {
    $token = $loginResult['token'];
    echo "Token: $token\n\n";
    
    // Test 2: Test auth with fresh token
    echo "=== Testing Auth Route ===\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/test-auth');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Test Auth Response Code: $httpCode\n";
    echo "Test Auth Response: $response\n\n";
    
    // Test 3: Test users route
    echo "=== Testing Users Route ===\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/users');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Users Route Response Code: $httpCode\n";
    echo "Users Route Response: $response\n";
} else {
    echo "Login failed - no token received\n";
}