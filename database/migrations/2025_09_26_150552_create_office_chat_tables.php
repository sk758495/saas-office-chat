<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Companies table
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->enum('plan', ['free', 'paid'])->default('free');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->integer('max_users')->default(5);
            $table->integer('max_storage_mb')->default(100);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_token')->nullable();
            $table->timestamp('email_verification_expires_at')->nullable();
            $table->timestamp('subscription_expires_at')->nullable();
            $table->decimal('subscription_amount', 8, 2)->default(0);
            $table->timestamps();
        });

        // Admins table
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['super_admin', 'company_admin'])->default('company_admin');
            $table->rememberToken();
            $table->timestamps();
        });

        // Departments table
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(true);
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Designations table
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Users table (update existing)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade')->after('email_verified_at');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null')->after('company_id');
            $table->foreignId('designation_id')->nullable()->constrained()->onDelete('set null')->after('department_id');
            $table->string('profile_photo')->nullable()->after('designation_id');
            $table->text('bio')->nullable()->after('profile_photo');
            $table->boolean('is_online')->default(false)->after('bio');
            $table->timestamp('last_seen')->nullable()->after('is_online');
            $table->boolean('chat_lock_enabled')->default(false)->after('last_seen');
            $table->string('chat_pin')->nullable()->after('chat_lock_enabled');
            $table->string('otp')->nullable()->after('chat_pin');
            $table->timestamp('otp_expires_at')->nullable()->after('otp');
        });

        // Groups table
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('profile_picture')->nullable();
            $table->timestamps();
        });

        // Group members table
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['admin', 'member'])->default('member');
            $table->timestamps();
            $table->unique(['group_id', 'user_id']);
        });

        // Chats table
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user2_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->unique(['user1_id', 'user2_id']);
        });

        // Messages table
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('chat_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('message')->nullable();
            $table->enum('type', ['text', 'image', 'file'])->default('text');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->json('folder_contents')->nullable();
            $table->boolean('is_folder')->default(false);
            $table->string('original_folder_name')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('chats');
        Schema::dropIfExists('group_members');
        Schema::dropIfExists('groups');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id', 'department_id', 'designation_id']);
            $table->dropColumn(['company_id', 'department_id', 'designation_id', 'profile_photo', 'bio', 'is_online', 'last_seen', 'chat_lock_enabled', 'chat_pin', 'otp', 'otp_expires_at']);
        });
        Schema::dropIfExists('designations');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('companies');
    }
};