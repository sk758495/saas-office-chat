<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->json('folder_contents')->nullable()->after('file_type');
            $table->boolean('is_folder')->default(false)->after('folder_contents');
            $table->string('original_folder_name')->nullable()->after('is_folder');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['folder_contents', 'is_folder', 'original_folder_name']);
        });
    }
};