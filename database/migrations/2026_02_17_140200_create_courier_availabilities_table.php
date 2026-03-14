<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_id')->unique()->constrained('couriers')->cascadeOnDelete();
            $table->boolean('is_online')->default(false);
            $table->string('zone')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->unsignedInteger('active_load')->default(0);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_availabilities');
    }
};

