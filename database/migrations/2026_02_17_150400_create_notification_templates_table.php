<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('event_key', 80);
            $table->string('channel', 30); // sms, email, push
            $table->string('subject')->nullable();
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->json('variables')->nullable();
            $table->timestamps();

            $table->unique(['event_key', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};

