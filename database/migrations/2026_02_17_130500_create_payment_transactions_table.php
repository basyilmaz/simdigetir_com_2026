<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('pricing_quote_id')->nullable()->constrained('pricing_quotes')->nullOnDelete();
            $table->string('provider', 40);
            $table->string('provider_reference')->nullable();
            $table->unsignedBigInteger('amount');
            $table->string('currency', 10)->default('TRY');
            $table->string('status', 40)->default('pending');
            $table->json('request_payload')->nullable();
            $table->json('callback_payload')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['provider', 'provider_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};

