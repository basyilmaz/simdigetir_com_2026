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
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('landing_pages')->cascadeOnDelete();
            $table->string('key');
            $table->string('type');
            $table->string('title')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['page_id', 'key']);
            $table->index(['page_id', 'is_active', 'sort_order']);
        });

        Schema::create('landing_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('landing_page_sections')->cascadeOnDelete();
            $table->string('item_key')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['section_id', 'is_active', 'sort_order']);
        });

        Schema::create('landing_section_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('landing_pages')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('landing_page_sections')->nullOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('snapshot');
            $table->string('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_section_revisions');
        Schema::dropIfExists('landing_section_items');
        Schema::dropIfExists('landing_page_sections');
        Schema::dropIfExists('landing_pages');
    }
};
