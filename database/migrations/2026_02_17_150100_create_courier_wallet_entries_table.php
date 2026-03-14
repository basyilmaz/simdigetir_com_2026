<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_wallet_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_id')->constrained('couriers')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('order_assignment_id')->nullable()->constrained('order_assignments')->nullOnDelete();
            $table->foreignId('settlement_batch_id')->nullable()->constrained('settlement_batches')->nullOnDelete();
            $table->string('entry_type', 30); // earning, commission, penalty, bonus
            $table->bigInteger('amount'); // signed minor units
            $table->bigInteger('balance_after')->default(0);
            $table->string('currency', 10)->default('TRY');
            $table->json('metadata')->nullable();
            $table->timestamp('entry_at')->useCurrent();
            $table->timestamps();

            $table->index(['courier_id', 'entry_at']);
            $table->index(['order_assignment_id', 'entry_type']);
            $table->unique(['order_assignment_id', 'entry_type'], 'wallet_assignment_entry_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_wallet_entries');
    }
};

