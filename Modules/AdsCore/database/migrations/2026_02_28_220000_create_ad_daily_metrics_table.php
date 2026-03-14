<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_daily_metrics', function (Blueprint $table): void {
            $table->id();
            $table->date('metric_date');
            $table->string('platform', 32);
            $table->foreignId('ad_campaign_id')->nullable()->constrained('ad_campaigns')->nullOnDelete();
            $table->string('campaign_name')->nullable();
            $table->decimal('spend', 12, 2)->default(0);
            $table->unsignedInteger('impressions')->default(0);
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedInteger('leads')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->decimal('roas', 12, 4)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['metric_date', 'platform', 'ad_campaign_id'], 'ad_daily_metrics_unique');
            $table->index(['metric_date', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_daily_metrics');
    }
};
