<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_connections', function (Blueprint $table): void {
            $table->id();
            $table->string('platform', 32);
            $table->string('name');
            $table->string('external_account_id')->nullable();
            $table->string('status', 32)->default('draft');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['platform', 'status']);
        });

        Schema::create('ad_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ad_connection_id')->constrained('ad_connections')->cascadeOnDelete();
            $table->string('platform', 32);
            $table->string('name');
            $table->string('objective', 64)->nullable();
            $table->string('status', 32)->default('draft');
            $table->string('external_campaign_id')->nullable();
            $table->decimal('daily_budget', 12, 2)->nullable();
            $table->string('currency', 8)->default('TRY');
            $table->json('targeting')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['platform', 'status']);
            $table->index(['external_campaign_id']);
        });

        Schema::create('ad_events', function (Blueprint $table): void {
            $table->id();
            $table->string('event_name', 64);
            $table->string('source', 64)->nullable();
            $table->string('medium', 64)->nullable();
            $table->string('campaign', 128)->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->decimal('value', 12, 2)->nullable();
            $table->string('currency', 8)->nullable();
            $table->string('external_id')->nullable();
            $table->string('gclid')->nullable();
            $table->string('fbclid')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['event_name', 'created_at']);
            $table->index(['external_id']);
        });

        Schema::create('ad_conversions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ad_campaign_id')->nullable()->constrained('ad_campaigns')->nullOnDelete();
            $table->foreignId('ad_event_id')->nullable()->constrained('ad_events')->nullOnDelete();
            $table->string('platform', 32);
            $table->string('event_name', 64);
            $table->string('status', 32)->default('pending');
            $table->decimal('value', 12, 2)->nullable();
            $table->string('currency', 8)->nullable();
            $table->string('external_id')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamp('pushed_at')->nullable();
            $table->timestamps();

            $table->index(['platform', 'status']);
            $table->index(['external_id']);
        });

        Schema::create('ad_sync_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('platform', 32);
            $table->string('action', 64);
            $table->string('status', 32)->default('pending');
            $table->string('target_type', 64)->nullable();
            $table->string('target_id')->nullable();
            $table->text('error_message')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->unsignedInteger('attempt_count')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['platform', 'action', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_sync_logs');
        Schema::dropIfExists('ad_conversions');
        Schema::dropIfExists('ad_events');
        Schema::dropIfExists('ad_campaigns');
        Schema::dropIfExists('ad_connections');
    }
};
