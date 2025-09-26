<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo')->nullable();
            $table->string('chat_pin', 4)->nullable();
            $table->boolean('chat_lock_enabled')->default(false);
            $table->string('pin_reset_token')->nullable();
            $table->timestamp('pin_reset_expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_photo', 'chat_pin', 'chat_lock_enabled', 'pin_reset_token', 'pin_reset_expires_at']);
        });
    }
};