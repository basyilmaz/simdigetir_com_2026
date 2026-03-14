<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_no', 40)->unique();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('request_snapshot');
            $table->json('resolved_rules')->nullable();
            $table->unsignedBigInteger('subtotal_amount')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('surge_amount')->default(0);
            $table->unsignedBigInteger('total_amount')->default(0);
            $table->string('currency', 10)->default('TRY');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_quotes');
    }
};

