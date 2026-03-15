<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete();
            $table->string('stage', 30);
            $table->string('proof_type', 30);
            $table->string('proof_value', 255)->nullable();
            $table->string('file_url', 500)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['order_id', 'stage', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_proofs');
    }
};
