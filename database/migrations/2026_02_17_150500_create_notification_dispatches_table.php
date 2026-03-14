<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_template_id')->nullable()->constrained('notification_templates')->nullOnDelete();
            $table->string('event_key', 80);
            $table->string('channel', 30);
            $table->string('target', 190);
            $table->string('status', 30)->default('sent');
            $table->text('error_message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('dispatched_at')->useCurrent();
            $table->timestamps();

            $table->index(['event_key', 'channel', 'dispatched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_dispatches');
    }
};

