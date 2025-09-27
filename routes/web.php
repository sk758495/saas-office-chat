<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\CompanyController;

Route::get('/', function () {
    return view('welcome');
});

// Company Registration Routes
Route::get('/company/register', [\App\Http\Controllers\CompanyController::class, 'register'])->name('company.register');
Route::post('/company/register', [\App\Http\Controllers\CompanyController::class, 'store'])->name('company.store');
Route::get('/company/verify-email', [\App\Http\Controllers\CompanyController::class, 'showVerifyEmail'])->name('company.verify-email');
Route::post('/company/verify-email', [\App\Http\Controllers\CompanyController::class, 'verifyEmail'])->name('company.verify-email.submit');
Route::get('/company/resend-otp', [\App\Http\Controllers\CompanyController::class, 'resendOtp'])->name('company.resend-otp');

Route::get('/dashboard', function () {
    return redirect()->route('chat.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Chat Routes
Route::middleware(['auth', 'verified', 'company.access'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{user}', [ChatController::class, 'show']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::get('/api/users', [ChatController::class, 'getUsers']);
    Route::get('/api/unread-counts', [ChatController::class, 'getUnreadCounts']);
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
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
    
    // Group Routes
    Route::post('/groups', [GroupController::class, 'create']);
    Route::get('/groups/{id}', [GroupController::class, 'show']);
    Route::post('/groups/send-message', [GroupController::class, 'sendMessage']);
    Route::post('/groups/{id}/add-member', [GroupController::class, 'addMember']);
    Route::post('/groups/{id}/remove-member', [GroupController::class, 'removeMember']);
    Route::post('/groups/{id}/make-admin', [GroupController::class, 'makeAdmin']);
    Route::post('/groups/{id}/photo', [GroupController::class, 'updateGroupPhoto']);
    Route::post('/groups/{id}/exit', [GroupController::class, 'exitGroup']);
    
    // File Routes
    Route::get('/files/view/{id}', [ChatController::class, 'viewFile']);
    Route::get('/files/download/{id}', [ChatController::class, 'downloadFile']);
    
    // API Routes
    Route::get('/api/unread-counts', [ChatController::class, 'getUnreadCounts']);
    Route::get('/api/group-unread-counts', [ChatController::class, 'getGroupUnreadCounts']);
    Route::get('/api/users', [ChatController::class, 'getUsers']);
    
    // Call Routes
    Route::post('/api/calls/initiate', [\App\Http\Controllers\CallController::class, 'initiateCall']);
    Route::post('/api/calls/{call}/join', [\App\Http\Controllers\CallController::class, 'joinCall']);
    Route::post('/api/calls/{call}/leave', [\App\Http\Controllers\CallController::class, 'leaveCall']);
    Route::post('/api/calls/{call}/decline', [\App\Http\Controllers\CallController::class, 'declineCall']);
    Route::post('/api/calls/{call}/start-recording', [\App\Http\Controllers\CallController::class, 'startRecording']);
    Route::post('/api/recordings/{recording}/stop', [\App\Http\Controllers\CallController::class, 'stopRecording']);
    Route::post('/api/recordings/{recording}/upload', [\App\Http\Controllers\CallController::class, 'uploadRecording']);
    Route::get('/api/calls/history', [\App\Http\Controllers\CallController::class, 'getCallHistory']);
    Route::get('/api/calls/{call}/recordings', [\App\Http\Controllers\CallController::class, 'getCallRecordings']);
    Route::get('/api/recordings/{recording}/download', [\App\Http\Controllers\CallController::class, 'downloadRecording']);
});

// API Routes for AJAX
Route::get('/api/designations/{department}', [DesignationController::class, 'getByDepartment']);

// Company Admin Routes
Route::middleware(['auth:admin'])->prefix('company')->name('company.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\CompanyController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [\App\Http\Controllers\CompanyController::class, 'settings'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\CompanyController::class, 'updateSettings'])->name('settings.update');
    Route::post('/upgrade', [\App\Http\Controllers\CompanyController::class, 'upgrade'])->name('upgrade');
    Route::post('/chat-theme', [\App\Http\Controllers\CompanyController::class, 'updateChatTheme'])->name('chat-theme.update');
    Route::post('/password', [\App\Http\Controllers\CompanyController::class, 'updatePassword'])->name('password.update');
});

// Admin Routes
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('company.dashboard');
    })->name('dashboard');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('departments', DepartmentController::class);
    Route::resource('designations', DesignationController::class);
    Route::get('/chat-monitor', [\App\Http\Controllers\Admin\ChatMonitorController::class, 'index'])->name('chat-monitor');
    Route::get('/chat-monitor/{type}/{id}', [\App\Http\Controllers\Admin\ChatMonitorController::class, 'show'])->name('chat-monitor.show');
    Route::get('/export/{type}', [\App\Http\Controllers\Admin\ExportController::class, 'exportData'])->name('export');
});

require __DIR__.'/auth.php';
require __DIR__.'/Admin/auth.php';

Route::get('auth/otp/verify', [OtpVerificationController::class, 'show'])->name('auth.otp.verify');
Route::post('auth/otp/verify', [OtpVerificationController::class, 'verify']);
Route::post('auth/otp/resend', [OtpVerificationController::class, 'resend'])->name('auth.otp.resend');

