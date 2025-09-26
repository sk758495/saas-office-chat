<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->enum('plan', ['free', 'paid'])->default('free');
            $table->boolean('is_active')->default(true);
            $table->integer('max_users')->default(5); // 5 for free, unlimited for paid
            $table->integer('max_storage_mb')->default(100); // 100MB for free, unlimited for paid
            $table->timestamp('subscription_expires_at')->nullable();
            $table->decimal('subscription_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};