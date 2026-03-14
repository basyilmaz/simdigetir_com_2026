<?php

namespace Modules\AdsCore\Services;

use Modules\AdsCore\Jobs\PushConversionJob;
use Modules\AdsCore\Models\AdConnection;
use Modules\AdsCore\Models\AdConversion;
use Modules\AdsCore\Models\AdEvent;
use Modules\Leads\Models\Lead;

class ConversionPipelineService
{
    public function captureLead(Lead $lead, array $context = []): void
    {
        if (! (bool) config('adscore.conversion.enabled', true)) {
            return;
        }

        $event = AdEvent::create([
            'event_name' => 'lead_submitted',
            'source' => $lead->source,
            'medium' => $lead->medium,
            'campaign' => $lead->campaign,
            'lead_id' => $lead->id,
            'external_id' => $context['external_id'] ?? ('lead-'.$lead->id),
            'gclid' => $context['gclid'] ?? null,
            'fbclid' => $context['fbclid'] ?? null,
            'payload' => [
                'lead_type' => $lead->type,
                'page_url' => $lead->page_url,
                'referrer' => $lead->referrer,
                'client_ip_address' => $context['client_ip_address'] ?? null,
                'client_user_agent' => $context['client_user_agent'] ?? null,
            ],
        ]);

        $platforms = $this->resolvePlatforms($lead->source, $lead->medium, $context);
        foreach ($platforms as $platform) {
            $conversion = AdConversion::create([
                'ad_event_id' => $event->id,
                'platform' => $platform,
                'event_name' => 'lead_submitted',
                'status' => 'pending',
                'value' => null,
                'currency' => 'TRY',
                'external_id' => 'lead-'.$lead->id.'-'.$platform,
            ]);

            $this->autoPushIfEnabled($conversion);
        }
    }

    private function autoPushIfEnabled(AdConversion $conversion): void
    {
        $runtime = $this->resolveAutoPushRuntime();

        if (! $runtime['auto_push']) {
            return;
        }

        $platforms = $runtime['auto_push_platforms'];
        if (! is_array($platforms) || ! in_array($conversion->platform, $platforms, true)) {
            return;
        }

        $mode = $runtime['auto_push_mode'];
        if ($mode === 'queue') {
            PushConversionJob::dispatch($conversion->id);
            return;
        }

        app(CampaignLifecycleService::class)->pushConversionById($conversion->id);
    }

    /**
     * @return array{auto_push: bool, auto_push_mode: string, auto_push_platforms: array<int, string>}
     */
    private function resolveAutoPushRuntime(): array
    {
        $platforms = $this->normalizePlatformList(config('adscore.conversion.auto_push_platforms', ['meta']));
        if ($platforms === []) {
            $platforms = ['meta'];
        }

        $runtime = [
            'auto_push' => (bool) config('adscore.conversion.auto_push', false),
            'auto_push_mode' => $this->normalizeAutoPushMode((string) config('adscore.conversion.auto_push_mode', 'sync')),
            'auto_push_platforms' => $platforms,
        ];

        $meta = $this->resolveMetaConnectionMeta();
        if ($meta === []) {
            return $runtime;
        }

        if (array_key_exists('auto_push', $meta)) {
            $runtime['auto_push'] = $this->toBool($meta['auto_push'], $runtime['auto_push']);
        }

        if (array_key_exists('auto_push_mode', $meta)) {
            $runtime['auto_push_mode'] = $this->normalizeAutoPushMode((string) $meta['auto_push_mode']);
        }

        if (array_key_exists('auto_push_platforms', $meta)) {
            $overridePlatforms = $this->normalizePlatformList($meta['auto_push_platforms']);
            if ($overridePlatforms !== []) {
                $runtime['auto_push_platforms'] = $overridePlatforms;
            }
        }

        return $runtime;
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveMetaConnectionMeta(): array
    {
        $connection = AdConnection::query()
            ->where('platform', 'meta')
            ->whereIn('status', ['connected', 'draft'])
            ->latest('id')
            ->first();

        return is_array($connection?->meta) ? $connection->meta : [];
    }

    /**
     * @return array<int, string>
     */
    private function normalizePlatformList(mixed $value): array
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }

        if (! is_array($value)) {
            return [];
        }

        $platforms = array_filter(array_map(
            static fn (mixed $platform): string => strtolower(trim((string) $platform)),
            $value
        ));

        return array_values(array_unique($platforms));
    }

    private function normalizeAutoPushMode(string $mode): string
    {
        $normalized = strtolower(trim($mode));

        return $normalized === 'queue' ? 'queue' : 'sync';
    }

    private function toBool(mixed $value, bool $default): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1 ? true : ($value === 0 ? false : $default);
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));

            if (in_array($normalized, ['1', 'true', 'yes', 'on'], true)) {
                return true;
            }

            if (in_array($normalized, ['0', 'false', 'no', 'off'], true)) {
                return false;
            }
        }

        return $default;
    }

    /**
     * @return array<int, string>
     */
    private function resolvePlatforms(?string $source, ?string $medium, array $context = []): array
    {
        $gclid = trim((string) ($context['gclid'] ?? ''));
        $fbclid = trim((string) ($context['fbclid'] ?? ''));

        if ($gclid !== '' && $fbclid === '') {
            return ['google'];
        }

        if ($fbclid !== '' && $gclid === '') {
            return ['meta'];
        }

        if ($gclid !== '' && $fbclid !== '') {
            return ['google', 'meta'];
        }

        $value = strtolower(trim((string) $source.' '.$medium));

        $platforms = [];
        if (str_contains($value, 'google') || str_contains($value, 'gads')) {
            $platforms[] = 'google';
        }
        if (str_contains($value, 'meta') || str_contains($value, 'facebook') || str_contains($value, 'instagram')) {
            $platforms[] = 'meta';
        }

        if (empty($platforms)) {
            $platforms[] = 'mock';
        }

        return array_values(array_unique($platforms));
    }
}
