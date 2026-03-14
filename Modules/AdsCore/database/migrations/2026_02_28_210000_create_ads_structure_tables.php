<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_adsets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ad_campaign_id')->constrained('ad_campaigns')->cascadeOnDelete();
            $table->string('platform', 32);
            $table->string('name');
            $table->string('status', 32)->default('draft');
            $table->string('external_adset_id')->nullable();
            $table->json('targeting')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['platform', 'status']);
            $table->index(['external_adset_id']);
        });

        Schema::create('ad_ads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ad_campaign_id')->constrained('ad_campaigns')->cascadeOnDelete();
            $table->foreignId('ad_adset_id')->nullable()->constrained('ad_adsets')->nullOnDelete();
            $table->string('platform', 32);
            $table->string('name');
            $table->string('status', 32)->default('draft');
            $table->string('external_ad_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['platform', 'status']);
            $table->index(['external_ad_id']);
        });

        Schema::create('ad_creatives', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ad_ad_id')->constrained('ad_ads')->cascadeOnDelete();
            $table->string('platform', 32);
            $table->string('name');
            $table->string('status', 32)->default('draft');
            $table->string('external_creative_id')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['platform', 'status']);
            $table->index(['external_creative_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_creatives');
        Schema::dropIfExists('ad_ads');
        Schema::dropIfExists('ad_adsets');
    }
};
