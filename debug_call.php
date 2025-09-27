<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Chat;
use App\Models\Group;

echo "=== Call Debug Information ===\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "No users found!\n";
    exit;
}

echo "User: {$user->name} (ID: {$user->id})\n";

// Get chats for this user
$chats = Chat::where('user1_id', $user->id)
    ->orWhere('user2_id', $user->id)
    ->get();

echo "Chats for user:\n";
foreach ($chats as $chat) {
    echo "  Chat ID: {$chat->id} (User1: {$chat->user1_id}, User2: {$chat->user2_id})\n";
}

// Get groups for this user
$groups = Group::whereHas('members', function($query) use ($user) {
    $query->where('user_id', $user->id);
})->get();

echo "Groups for user:\n";
foreach ($groups as $group) {
    echo "  Group ID: {$group->id} (Name: {$group->name})\n";
}

echo "\n=== Testing Call Data ===\n";

// Test data for one-to-one call
if ($chats->count() > 0) {
    $testChat = $chats->first();
    echo "Test one-to-one call data:\n";
    echo "{\n";
    echo "  \"type\": \"one_to_one\",\n";
    echo "  \"call_type\": \"video\",\n";
    echo "  \"chat_id\": {$testChat->id},\n";
    echo "  \"group_id\": null\n";
    echo "}\n\n";
}

// Test data for group call
if ($groups->count() > 0) {
    $testGroup = $groups->first();
    echo "Test group call data:\n";
    echo "{\n";
    echo "  \"type\": \"group\",\n";
    echo "  \"call_type\": \"video\",\n";
    echo "  \"chat_id\": null,\n";
    echo "  \"group_id\": {$testGroup->id}\n";
    echo "}\n\n";
}