<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_transaction_id')->constrained('payment_transactions')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->unsignedBigInteger('amount');
            $table->string('currency', 10)->default('TRY');
            $table->string('status', 40)->default('succeeded');
            $table->string('provider_reference', 120)->nullable();
            $table->string('reason', 255)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('processed_at')->useCurrent();
            $table->timestamps();

            $table->index(['payment_transaction_id', 'processed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_refunds');
    }
};

