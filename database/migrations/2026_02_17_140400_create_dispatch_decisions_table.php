<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete();
            $table->string('decision_type', 30); // auto_assign, manual_assign, reassign
            $table->string('result', 30); // assigned, skipped, failed
            $table->unsignedInteger('score')->nullable();
            $table->string('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('decided_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_decisions');
    }
};

