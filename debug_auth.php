<?php

// Debug authentication script
$baseUrl = 'http://127.0.0.1:8000/api';

// Test with existing user - you'll need to know the password
echo "=== Testing Authentication Debug ===\n";

// First, let's test the test-auth route with your token
echo "Enter your Bearer token (without 'Bearer ' prefix): ";
$token = trim(fgets(STDIN));

if (empty($token)) {
    echo "No token provided. Exiting.\n";
    exit;
}

echo "\n=== Testing with provided token ===\n";

// Test 1: Test auth route
echo "Testing /api/test-auth...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/test-auth');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Test Auth Response Code: $httpCode\n";
echo "Test Auth Response: $response\n\n";

// Test 2: Test users route
echo "Testing /api/users...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/users');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Users Route Response Code: $httpCode\n";
echo "Users Route Response: $response\n\n";

// Test 3: Test me route
echo "Testing /api/me...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/me');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Me Route Response Code: $httpCode\n";
echo "Me Route Response: $response\n";