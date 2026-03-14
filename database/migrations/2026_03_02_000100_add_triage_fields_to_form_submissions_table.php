<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('form_submissions', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('form_submissions', 'follow_up_at')) {
                $table->timestamp('follow_up_at')->nullable()->after('assigned_to');
            }

            if (! Schema::hasColumn('form_submissions', 'internal_note')) {
                $table->text('internal_note')->nullable()->after('follow_up_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('form_submissions', 'internal_note')) {
                $table->dropColumn('internal_note');
            }

            if (Schema::hasColumn('form_submissions', 'follow_up_at')) {
                $table->dropColumn('follow_up_at');
            }

            if (Schema::hasColumn('form_submissions', 'assigned_to')) {
                $table->dropConstrainedForeignId('assigned_to');
            }
        });
    }
};
