<?php

namespace Modules\Reporting\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Reporting\Services\AdsReportingService;

class AggregateDailyAdsMetricsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly ?string $metricDate = null)
    {
    }

    public function handle(AdsReportingService $reporting): void
    {
        $date = $this->metricDate ?? now()->subDay()->toDateString();
        $reporting->aggregateDailyMetrics($date);
    }
}
