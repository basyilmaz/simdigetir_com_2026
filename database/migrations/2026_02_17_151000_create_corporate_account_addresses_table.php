<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_account_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_account_id')->constrained('corporate_accounts')->cascadeOnDelete();
            $table->string('label', 80);
            $table->text('address');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_account_addresses');
    }
};

