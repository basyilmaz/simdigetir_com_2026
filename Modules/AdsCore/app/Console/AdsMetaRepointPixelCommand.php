<?php

namespace Modules\AdsCore\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\AdsCore\Models\AdCampaign;
use Modules\AdsCore\Models\AdConnection;
use Modules\Settings\Models\Setting;

class AdsMetaRepointPixelCommand extends Command
{
    private const SITE_PIXEL_SETTING = 'marketing.meta_pixel_id';

    protected $signature = 'ads:meta-repoint-pixel
        {old : Current Meta pixel ID}
        {new : Target Meta pixel ID}
        {--dry-run : Report affected records without writing}';

    protected $description = 'Repoints Meta pixel references on ad connections, campaigns and the site pixel setting.';

    public function handle(): int
    {
        $old = trim((string) $this->argument('old'));
        $new = trim((string) $this->argument('new'));

        if (! ctype_digit($old) || ! ctype_digit($new) || $old === $new) {
            $this->error('old and new must be different numeric pixel IDs.');

            return self::INVALID;
        }

        $connections = $this->matchingRecords(AdConnection::class, $old);
        $campaigns = $this->matchingRecords(AdCampaign::class, $old);
        $setting = Setting::query()->where('key', self::SITE_PIXEL_SETTING)->first();
        $settingMatches = $setting !== null && (string) $setting->value === $old;

        $dryRun = (bool) $this->option('dry-run');
        $prefix = $dryRun ? '[dry-run] affected' : 'updated';

        if (! $dryRun) {
            DB::transaction(function () use ($connections, $campaigns, $setting, $settingMatches, $new): void {
                foreach ([...$connections, ...$campaigns] as $record) {
                    $meta = $record->meta;
                    $meta['pixel_id'] = $new;
                    $record->forceFill(['meta' => $meta])->save();
                }

                if ($settingMatches) {
                    $setting->forceFill(['value' => $new])->save();
                }
            });

            if ($settingMatches) {
                Setting::forgetCache(self::SITE_PIXEL_SETTING);
            }
        }

        $this->info(sprintf('%s AdConnection: %d', $prefix, count($connections)));
        $this->info(sprintf('%s Campaign: %d', $prefix, count($campaigns)));
        $this->info(sprintf('%s Setting (%s): %d', $prefix, self::SITE_PIXEL_SETTING, $settingMatches ? 1 : 0));

        return self::SUCCESS;
    }

    /**
     * @param  class-string<Model>  $model
     * @return array<int, Model>
     */
    private function matchingRecords(string $model, string $old): array
    {
        return $model::query()
            ->whereNotNull('meta')
            ->get()
            ->filter(fn (Model $record): bool => (string) data_get($record->meta, 'pixel_id', '') === $old)
            ->values()
            ->all();
    }
}
