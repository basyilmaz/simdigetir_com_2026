<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rule_type', 40);
            $table->unsignedInteger('priority')->default(100);
            $table->json('conditions')->nullable();
            $table->json('effect')->nullable();
            $table->timestamp('active_from')->nullable();
            $table->timestamp('active_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['rule_type', 'priority']);
            $table->index(['is_active', 'active_from', 'active_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};

