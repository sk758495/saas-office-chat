<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('password_reset_otp')->nullable()->after('password');
            $table->timestamp('password_reset_expires_at')->nullable()->after('password_reset_otp');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['password_reset_otp', 'password_reset_expires_at']);
        });
    }
};