<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Calls table
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->string('call_id')->unique();
            $table->enum('type', ['one_to_one', 'group'])->default('one_to_one');
            $table->enum('call_type', ['audio', 'video'])->default('video');
            $table->enum('status', ['initiated', 'ringing', 'active', 'ended', 'missed', 'declined'])->default('initiated');
            $table->foreignId('caller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('chat_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration')->default(0); // in seconds
            $table->json('participants')->nullable(); // store participant data
            $table->timestamps();
        });

        // Call participants table
        Schema::create('call_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['invited', 'joined', 'left', 'declined'])->default('invited');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
        });

        // Call recordings table
        Schema::create('call_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size')->default(0); // in bytes
            $table->integer('duration')->default(0); // in seconds
            $table->enum('status', ['recording', 'completed', 'failed'])->default('recording');
            $table->foreignId('started_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_recordings');
        Schema::dropIfExists('call_participants');
        Schema::dropIfExists('calls');
    }
};