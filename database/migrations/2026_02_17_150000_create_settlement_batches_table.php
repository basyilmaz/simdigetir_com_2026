<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlement_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no', 40)->unique();
            $table->string('status', 30)->default('closed');
            $table->unsignedBigInteger('net_amount')->default(0);
            $table->string('currency', 10)->default('TRY');
            $table->text('notes')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlement_batches');
    }
};

