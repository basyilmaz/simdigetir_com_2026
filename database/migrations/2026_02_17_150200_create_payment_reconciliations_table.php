<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_transaction_id')->constrained('payment_transactions')->cascadeOnDelete();
            $table->string('provider_status', 40);
            $table->string('internal_status', 40);
            $table->boolean('is_match')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('reconciled_at')->useCurrent();
            $table->timestamps();

            $table->index(['payment_transaction_id', 'reconciled_at'], 'pay_recon_tx_recon_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_reconciliations');
    }
};
