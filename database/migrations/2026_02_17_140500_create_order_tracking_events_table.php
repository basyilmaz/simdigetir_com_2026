<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete();
            $table->string('event_type', 40); // location, eta_update, status_note
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->unsignedInteger('eta_seconds')->nullable();
            $table->string('note')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_tracking_events');
    }
};

