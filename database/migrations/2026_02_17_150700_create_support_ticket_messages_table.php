<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->string('author_type', 30);
            $table->unsignedBigInteger('author_id')->nullable();
            $table->text('message');
            $table->boolean('is_internal')->default(false);
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_messages');
    }
};

