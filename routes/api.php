<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\WebSocketController;

// Public Routes with CORS
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// OPTIONS routes for CORS preflight
Route::options('/register', function() { return response('', 200)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'POST, OPTIONS')->header('Access-Control-Allow-Headers', 'Content-Type, Authorization'); });
Route::options('/login', function() { return response('', 200)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'POST, OPTIONS')->header('Access-Control-Allow-Headers', 'Content-Type, Authorization'); });
Route::options('/forgot-password', function() { return response('', 200)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'POST, OPTIONS')->header('Access-Control-Allow-Headers', 'Content-Type, Authorization'); });
Route::options('/reset-password', function() { return response('', 200)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'POST, OPTIONS')->header('Access-Control-Allow-Headers', 'Content-Type, Authorization'); });

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Chat Routes
    Route::get('/chat', [ChatController::class, 'index']);
    Route::get('/chat/{user}', [ChatController::class, 'show']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::get('/messages', [ChatController::class, 'getAllMessages']);
    Route::get('/messages/{id}', [ChatController::class, 'getMessage']);
    Route::delete('/messages/{id}', [ChatController::class, 'deleteMessage']);
    Route::get('/files/view/{id}', [ChatController::class, 'viewFile']);
    Route::get('/files/download/{id}', [ChatController::class, 'downloadFile']);
    Route::get('/users', [ChatController::class, 'getUsers']);
    Route::get('/unread-counts', [ChatController::class, 'getUnreadCounts']);
    Route::get('/group-unread-counts', [ChatController::class, 'getGroupUnreadCounts']);
    
    // Group Routes
    Route::get('/groups', [GroupController::class, 'index']);
    Route::post('/groups', [GroupController::class, 'create']);
    Route::get('/groups/{id}', [GroupController::class, 'show']);
    Route::post('/groups/send-message', [GroupController::class, 'sendMessage']);
    Route::post('/groups/{id}/add-member', [GroupController::class, 'addMember']);
    Route::post('/groups/{id}/remove-member', [GroupController::class, 'removeMember']);
    Route::post('/groups/{id}/make-admin', [GroupController::class, 'makeAdmin']);
    Route::post('/groups/{id}/photo', [GroupController::class, 'updateGroupPhoto']);
    Route::post('/groups/{id}/exit', [GroupController::class, 'exitGroup']);
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto']);
    Route::post('/profile/pin', [ProfileController::class, 'updatePin']);
    Route::post('/profile/toggle-lock', [ProfileController::class, 'toggleLock']);
    Route::post('/profile/verify-pin', [ProfileController::class, 'verifyPin']);
    Route::post('/profile/forgot-pin', [ProfileController::class, 'forgotPin']);
    Route::post('/profile/verify-otp', [ProfileController::class, 'verifyOtp']);
    Route::post('/profile/reset-pin-after-otp', [ProfileController::class, 'resetPinAfterOtp']);
    Route::post('/profile/remove-pin', [ProfileController::class, 'removePin']);
    Route::post('/profile/send-current-email-verification', [ProfileController::class, 'sendCurrentEmailVerification']);
    Route::post('/profile/verify-current-email', [ProfileController::class, 'verifyCurrentEmail']);
    Route::post('/profile/email', [ProfileController::class, 'updateEmail']);
    Route::post('/profile/verify-new-email', [ProfileController::class, 'verifyNewEmail']);
    Route::post('/profile/password', [ProfileController::class, 'updatePassword']);

});

// Admin Routes (Protected by admin middleware)
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    
    // Departments
    Route::get('/departments', [AdminController::class, 'getDepartments']);
    Route::post('/departments', [AdminController::class, 'createDepartment']);
    Route::put('/departments/{department}', [AdminController::class, 'updateDepartment']);
    Route::delete('/departments/{department}', [AdminController::class, 'deleteDepartment']);
    
    // Designations
    Route::get('/designations', [AdminController::class, 'getDesignations']);
    Route::post('/designations', [AdminController::class, 'createDesignation']);
    Route::put('/designations/{designation}', [AdminController::class, 'updateDesignation']);
    Route::delete('/designations/{designation}', [AdminController::class, 'deleteDesignation']);
    Route::get('/designations/department/{departmentId}', [AdminController::class, 'getDesignationsByDepartment']);
    
    // Chat Monitor
    Route::get('/chat-monitor', [AdminController::class, 'getChatMonitor']);
    Route::get('/chat-monitor/{type}/{id}', [AdminController::class, 'getChatDetails']);
    
    // Users Management
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::get('/users/{id}', [AdminController::class, 'getUserDetails']);
    
    // Export Data
    Route::get('/export/users', [AdminController::class, 'exportUsers']);
    Route::get('/export/one-to-one-chats', [AdminController::class, 'exportOneToOneChats']);
    Route::get('/export/group-chats', [AdminController::class, 'exportGroupChats']);
    Route::get('/export/departments', [AdminController::class, 'exportDepartments']);
});

// Public Routes for Departments/Designations (for registration)
Route::get('/public/departments', [AdminController::class, 'getDepartments']);
Route::get('/public/designations/department/{departmentId}', [AdminController::class, 'getDesignationsByDepartment']);

// WebSocket Routes (with CORS support for Flutter)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/ws/connect', [WebSocketController::class, 'connect']);
    Route::post('/ws/broadcast', [WebSocketController::class, 'broadcast']);
});
Route::options('/ws/connect', function() { 
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
});
Route::options('/ws/broadcast', function() { 
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
});





// Test route to debug authentication
Route::middleware('auth:sanctum')->get('/test-auth', function (Request $request) {
    return response()->json([
        'success' => true,
        'user_id' => $request->user()->id,
        'user_name' => $request->user()->name,
        'message' => 'Authentication working'
    ]);
});

// Test route without authentication for basic testing
Route::get('/test-basic', function () {
    return response()->json([
        'success' => true,
        'message' => 'Basic API working',
        'timestamp' => now(),
        'server' => request()->getHost()
    ]);
});


