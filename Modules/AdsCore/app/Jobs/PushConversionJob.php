<?php

namespace Modules\AdsCore\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\AdsCore\Models\AdConversion;
use Modules\AdsCore\Models\AdSyncLog;
use Modules\AdsCore\Services\CampaignLifecycleService;
use Throwable;

class PushConversionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /**
     * @var array<int, int>
     */
    public array $backoff = [60, 300, 900];

    public function __construct(private readonly int $conversionId)
    {
    }

    public function handle(CampaignLifecycleService $lifecycle): void
    {
        $lifecycle->pushConversionById($this->conversionId);
    }

    public function failed(Throwable $exception): void
    {
        $conversion = AdConversion::query()->find($this->conversionId);
        if ($conversion === null) {
            return;
        }

        $conversion->forceFill([
            'status' => 'dead_letter',
            'response_payload' => [
                'error' => $exception->getMessage(),
                'failed_at' => now()->toIso8601String(),
            ],
        ])->save();

        AdSyncLog::query()->create([
            'platform' => $conversion->platform,
            'action' => 'push_conversion',
            'status' => 'dead_letter',
            'target_type' => 'conversion',
            'target_id' => (string) $conversion->id,
            'error_message' => $exception->getMessage(),
            'request_payload' => [
                'job' => static::class,
                'conversion_id' => $conversion->id,
            ],
            'response_payload' => null,
            'attempt_count' => $this->attempts(),
            'processed_at' => now(),
        ]);
    }
}
