<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_id')->constrained('couriers')->cascadeOnDelete();
            $table->string('document_type', 40);
            $table->string('file_url');
            $table->string('status', 30)->default('pending'); // pending, approved, rejected
            $table->text('review_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamps();

            $table->index(['courier_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_documents');
    }
};

