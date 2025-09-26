<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('chat_primary_color')->default('#ff6b35')->after('subscription_amount');
            $table->string('chat_secondary_color')->default('#f7931e')->after('chat_primary_color');
            $table->string('chat_theme_name')->default('Orange Sunset')->after('chat_secondary_color');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['chat_primary_color', 'chat_secondary_color', 'chat_theme_name']);
        });
    }
};