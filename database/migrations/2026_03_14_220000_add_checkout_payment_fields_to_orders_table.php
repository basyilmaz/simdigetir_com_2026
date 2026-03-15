<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 40)->nullable()->after('payment_state');
            $table->string('payment_timing', 40)->nullable()->after('payment_method');
            $table->string('payer_role', 40)->nullable()->after('payment_timing');
            $table->json('checkout_snapshot')->nullable()->after('price_breakdown');

            $table->index(['payment_method', 'payment_timing']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_payment_method_payment_timing_index');
            $table->dropColumn([
                'payment_method',
                'payment_timing',
                'payer_role',
                'checkout_snapshot',
            ]);
        });
    }
};
