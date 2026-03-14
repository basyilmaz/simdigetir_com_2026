<?php

namespace Modules\AdsCore\Console;

use Illuminate\Console\Command;
use Modules\AdsCore\Models\AdConnection;
use Modules\AdsCore\Models\AdSyncLog;

class AdsHealthCheckCommand extends Command
{
    protected $signature = 'ads:health-check {--hours=48 : Expiry threshold in hours}';

    protected $description = 'Checks ad token expiry and failed sync pipeline health.';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $threshold = now()->addHours($hours);

        $expiringTokens = AdConnection::query()
            ->where('status', 'connected')
            ->whereNotNull('token_expires_at')
            ->where('token_expires_at', '<=', $threshold)
            ->count();

        $failedSyncs = AdSyncLog::query()
            ->whereIn('status', ['failed', 'dead_letter'])
            ->where('created_at', '>=', now()->subDay())
            ->count();

        $this->line('expiring_tokens='.$expiringTokens);
        $this->line('failed_syncs='.$failedSyncs);

        if ($expiringTokens > 0 || $failedSyncs > 0) {
            $this->warn('ads_health=degraded');

            return self::FAILURE;
        }

        $this->info('ads_health=ok');

        return self::SUCCESS;
    }
}
