<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name');
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->string('vehicle_type', 40)->nullable();
            $table->string('status', 30)->default('pending'); // pending, approved, rejected, suspended
            $table->text('application_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('couriers');
    }
};

