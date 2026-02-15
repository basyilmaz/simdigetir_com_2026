<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['corporate_quote', 'courier_apply', 'contact'])->default('contact');
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            
            // UTM tracking
            $table->string('source')->nullable(); // utm_source
            $table->string('medium')->nullable(); // utm_medium
            $table->string('campaign')->nullable(); // utm_campaign
            $table->string('term')->nullable(); // utm_term
            $table->string('content')->nullable(); // utm_content
            $table->string('page_url')->nullable();
            $table->string('referrer')->nullable();
            
            // Status
            $table->enum('status', ['new', 'contacted', 'qualified', 'lost', 'won'])->default('new');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
