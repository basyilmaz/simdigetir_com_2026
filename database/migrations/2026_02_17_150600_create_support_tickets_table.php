<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no', 40)->unique();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete();
            $table->string('channel', 30)->default('web');
            $table->string('status', 30)->default('open');
            $table->string('priority', 30)->default('normal');
            $table->string('subject');
            $table->text('description');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};

