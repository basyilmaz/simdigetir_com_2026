<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tax_no', 40)->nullable();
            $table->string('tax_office', 120)->nullable();
            $table->string('invoice_email')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('status', 30)->default('active');
            $table->json('contract_meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_accounts');
    }
};

