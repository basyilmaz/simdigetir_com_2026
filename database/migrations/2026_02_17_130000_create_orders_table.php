<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('order_no', 40)->unique();
            $table->string('state', 40)->default('draft');
            $table->string('payment_state', 40)->default('pending');
            $table->string('pickup_name')->nullable();
            $table->string('pickup_phone', 30)->nullable();
            $table->text('pickup_address')->nullable();
            $table->decimal('pickup_lat', 10, 7)->nullable();
            $table->decimal('pickup_lng', 10, 7)->nullable();
            $table->string('dropoff_name')->nullable();
            $table->string('dropoff_phone', 30)->nullable();
            $table->text('dropoff_address')->nullable();
            $table->decimal('dropoff_lat', 10, 7)->nullable();
            $table->decimal('dropoff_lng', 10, 7)->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->unsignedInteger('distance_meters')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('vehicle_type', 40)->nullable();
            $table->json('notes')->nullable();
            $table->unsignedBigInteger('subtotal_amount')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('surge_amount')->default(0);
            $table->unsignedBigInteger('total_amount')->default(0);
            $table->string('currency', 10)->default('TRY');
            $table->json('price_breakdown')->nullable();
            $table->timestamps();

            $table->index(['state', 'created_at']);
            $table->index(['payment_state', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

