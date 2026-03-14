<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sitemap_entries', function (Blueprint $table) {
            $table->id();
            $table->string('path')->unique();
            $table->string('changefreq', 20)->default('monthly');
            $table->decimal('priority', 2, 1)->default(0.5);
            $table->boolean('is_active')->default(true);
            $table->timestamp('lastmod_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sitemap_entries');
    }
};

