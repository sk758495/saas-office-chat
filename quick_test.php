<?php

// Quick test to check token format
$baseUrl = 'http://127.0.0.1:8000/api';

// The token you mentioned in your error: 3|BEppqNSs8rKBNWBGd0PaIy6byuGs22aLKMn6UcQr9e8be671
$token = '3|BEppqNSs8rKBNWBGd0PaIy6byuGs22aLKMn6UcQr9e8be671';

echo "Testing with token: $token\n\n";

// Test the users route
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/users');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Response Code: $httpCode\n";
echo "Response: $response\n";