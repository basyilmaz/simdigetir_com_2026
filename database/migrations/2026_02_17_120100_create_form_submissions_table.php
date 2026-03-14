<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_definition_id')->constrained('form_definitions')->cascadeOnDelete();
            $table->json('payload');
            $table->string('status', 40)->default('received');
            $table->string('request_ip', 45)->nullable();
            $table->text('page_url')->nullable();
            $table->text('referrer')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['form_definition_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};

