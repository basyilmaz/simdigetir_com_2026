<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_document_id')->constrained('legal_documents')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->longText('content')->nullable();
            $table->text('summary')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['legal_document_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_document_versions');
    }
};

