<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_state_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('from_state', 40)->nullable();
            $table->string('to_state', 40);
            $table->string('actor_type', 40)->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['order_id', 'created_at']);
            $table->index(['to_state', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_state_logs');
    }
};

