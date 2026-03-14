<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('support_tickets', 'deleted_at')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        if (! Schema::hasColumn('couriers', 'deleted_at')) {
            Schema::table('couriers', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('support_tickets', 'deleted_at')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('couriers', 'deleted_at')) {
            Schema::table('couriers', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
