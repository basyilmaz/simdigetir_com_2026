<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('courier_id')->constrained('couriers')->cascadeOnDelete();
            $table->string('status', 30)->default('pending'); // pending, accepted, rejected, completed, cancelled
            $table->string('assignment_type', 30)->default('auto'); // auto, manual, reassignment
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->text('assignment_note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['courier_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_assignments');
    }
};

