<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First add nullable company_id columns
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable();
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable();
            $table->enum('role', ['super_admin', 'company_admin'])->default('company_admin');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable();
        });

        Schema::table('designations', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['company_id', 'role']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });

        Schema::table('designations', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};