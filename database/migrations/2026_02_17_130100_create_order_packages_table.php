<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('package_type', 40)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('weight_grams')->nullable();
            $table->unsignedInteger('length_cm')->nullable();
            $table->unsignedInteger('width_cm')->nullable();
            $table->unsignedInteger('height_cm')->nullable();
            $table->unsignedBigInteger('declared_value_amount')->nullable();
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_packages');
    }
};

