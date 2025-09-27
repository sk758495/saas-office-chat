<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add missing PIN reset fields
            if (!Schema::hasColumn('users', 'pin_reset_token')) {
                $table->string('pin_reset_token')->nullable()->after('chat_pin');
            }
            if (!Schema::hasColumn('users', 'pin_reset_expires_at')) {
                $table->timestamp('pin_reset_expires_at')->nullable()->after('pin_reset_token');
            }
            
            // Add missing email verification fields for profile updates
            if (!Schema::hasColumn('users', 'email_verification_token')) {
                $table->string('email_verification_token')->nullable()->after('pin_reset_expires_at');
            }
            if (!Schema::hasColumn('users', 'email_verification_expires_at')) {
                $table->timestamp('email_verification_expires_at')->nullable()->after('email_verification_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'pin_reset_token',
                'pin_reset_expires_at', 
                'email_verification_token',
                'email_verification_expires_at'
            ]);
        });
    }
};