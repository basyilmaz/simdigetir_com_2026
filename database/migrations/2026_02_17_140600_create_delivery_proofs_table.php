<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete();
            $table->string('proof_type', 30); // otp, signature, photo
            $table->string('proof_value')->nullable();
            $table->string('file_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['order_id', 'proof_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_proofs');
    }
};

