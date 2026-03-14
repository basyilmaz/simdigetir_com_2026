<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_account_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_account_id')->constrained('corporate_accounts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 30)->default('member');
            $table->timestamps();

            $table->unique(['corporate_account_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_account_users');
    }
};

