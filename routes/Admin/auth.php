<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.attempt');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::get('register', [AdminAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AdminAuthController::class, 'register'])->name('register.submit');

    Route::get('otp/verify', [AdminAuthController::class, 'showOtpForm'])->name('otp.form');
    Route::post('otp/verify', [AdminAuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('otp/resend', [AdminAuthController::class, 'resendOtp'])->name('otp.resend');

    Route::get('forgot-password', [AdminAuthController::class, 'showForgotPasswordForm'])->name('forgot-password');
    Route::post('forgot-password', [AdminAuthController::class, 'sendResetOtp'])->name('forgot-password.send');
    Route::get('reset-password', [AdminAuthController::class, 'showResetPasswordForm'])->name('reset-password');
    Route::post('reset-password', [AdminAuthController::class, 'resetPassword'])->name('reset-password.submit');
    
    Route::get('login-otp', [AdminAuthController::class, 'showLoginOtpForm'])->name('login-otp');
    Route::post('login-otp', [AdminAuthController::class, 'sendLoginOtp'])->name('login-otp.send');
    Route::post('login-otp-verify', [AdminAuthController::class, 'verifyLoginOtp'])->name('login-otp.verify');
});
