<?php

namespace Modules\AdsMeta\Services;

use Illuminate\Support\Facades\Http;
use Modules\AdsCore\Contracts\AdsProviderInterface;
use Modules\AdsCore\Models\AdCampaign;
use Modules\AdsCore\Models\AdConnection;
use Modules\AdsCore\Models\AdConversion;
use Modules\AdsCore\Models\AdEvent;
use Modules\Leads\Models\Lead;
use Throwable;

class MetaAdsProvider implements AdsProviderInterface
{
    public function platform(): string
    {
        return 'meta';
    }

    public function connect(AdConnection $connection): array
    {
        return [
            'success' => true,
            'external_account_id' => $connection->external_account_id ?: 'meta-'.$connection->id,
        ];
    }

    public function refreshToken(AdConnection $connection): array
    {
        return [
            'success' => true,
            'token_expires_at' => now()->addDays(60)->toIso8601String(),
        ];
    }

    public function createCampaign(AdCampaign $campaign): array
    {
        $campaignId = $campaign->external_campaign_id ?: 'meta-'.$campaign->id;

        return [
            'success' => true,
            'external_campaign_id' => $campaignId,
            'external_adset_id' => 'meta-adset-'.$campaign->id,
            'external_ad_id' => 'meta-ad-'.$campaign->id,
            'external_creative_id' => 'meta-creative-'.$campaign->id,
            'status' => 'active',
        ];
    }

    public function updateCampaign(AdCampaign $campaign): array
    {
        return [
            'success' => true,
            'external_campaign_id' => $campaign->external_campaign_id ?: 'meta-'.$campaign->id,
            'status' => $campaign->status,
        ];
    }

    public function pauseCampaign(AdCampaign $campaign): array
    {
        return [
            'success' => true,
            'external_campaign_id' => $campaign->external_campaign_id ?: 'meta-'.$campaign->id,
            'external_adset_id' => 'meta-adset-'.$campaign->id,
            'external_ad_id' => 'meta-ad-'.$campaign->id,
            'status' => 'paused',
        ];
    }

    public function pushConversion(AdConversion $conversion): array
    {
        $conversion->loadMissing(['campaign.connection', 'event']);

        $connection = $this->resolveConnection($conversion);
        $pixelId = $this->resolvePixelId($conversion, $connection);
        $accessToken = $this->resolveAccessToken($connection);

        if (! $this->isDirectModeEnabled($connection, $pixelId, $accessToken)) {
            return $this->mockConversionResponse($conversion, 'direct_mode_disabled_or_missing_credentials');
        }

        $eventId = $conversion->external_id ?: ('capi-'.$conversion->id.'-'.now()->timestamp);
        $eventPayload = $this->buildEventPayload($conversion, $eventId);
        $requestPayload = [
            'data' => json_encode([$eventPayload], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'access_token' => $accessToken,
        ];

        $testEventCode = $this->resolveTestEventCode($connection);
        if ($testEventCode !== '') {
            $requestPayload['test_event_code'] = $testEventCode;
        }

        $graphBaseUrl = rtrim((string) config('adscore.meta.graph_base_url', 'https://graph.facebook.com'), '/');
        $graphVersion = trim((string) config('adscore.meta.graph_version', 'v22.0'), '/');
        $timeout = (int) config('adscore.meta.timeout_seconds', 10);
        $endpoint = "{$graphBaseUrl}/{$graphVersion}/{$pixelId}/events";

        try {
            $response = Http::asForm()
                ->timeout(max(1, $timeout))
                ->post($endpoint, $requestPayload);

            $body = $response->json();
            if (! is_array($body)) {
                $body = ['raw' => (string) $response->body()];
            }

            $apiError = is_array($body['error'] ?? null) ? $body['error'] : null;
            $success = $response->successful() && $apiError === null;

            if (! $success) {
                return [
                    'success' => false,
                    'status' => 'failed',
                    'external_id' => $conversion->external_id ?: ('mconv-'.$conversion->id),
                    'capi_event_id' => $eventId,
                    'channel' => 'meta_capi',
                    'mode' => 'direct',
                    'http_status' => $response->status(),
                    'error' => (string) ($apiError['message'] ?? 'meta_capi_http_error'),
                    'response' => $body,
                ];
            }

            return [
                'success' => true,
                'status' => 'sent',
                'external_id' => $conversion->external_id ?: ('mconv-'.$conversion->id),
                'capi_event_id' => $eventId,
                'channel' => 'meta_capi',
                'mode' => 'direct',
                'events_received' => (int) ($body['events_received'] ?? 0),
                'fbtrace_id' => $body['fbtrace_id'] ?? null,
            ];
        } catch (Throwable $exception) {
            return [
                'success' => false,
                'status' => 'failed',
                'external_id' => $conversion->external_id ?: ('mconv-'.$conversion->id),
                'capi_event_id' => $eventId,
                'channel' => 'meta_capi',
                'mode' => 'direct',
                'error' => $exception->getMessage(),
            ];
        }

    }

    /**
     * @return array<string, mixed>
     */
    private function buildEventPayload(AdConversion $conversion, string $eventId): array
    {
        $event = $conversion->event;
        $lead = $this->resolveLead($event);
        $eventPayload = is_array($event?->payload) ? $event->payload : [];

        $eventSourceUrl = (string) ($eventPayload['page_url'] ?? $lead?->page_url ?? config('app.url'));
        $eventName = $this->mapEventName((string) $conversion->event_name);

        $userData = [];
        $hashedEmail = $this->hashNormalized($lead?->email);
        if ($hashedEmail !== null) {
            $userData['em'] = [$hashedEmail];
        }

        $normalizedPhone = $this->normalizePhone($lead?->phone);
        $hashedPhone = $this->hashNormalized($normalizedPhone);
        if ($hashedPhone !== null) {
            $userData['ph'] = [$hashedPhone];
        }

        $clientIp = trim((string) ($eventPayload['client_ip_address'] ?? ''));
        if ($clientIp !== '') {
            $userData['client_ip_address'] = $clientIp;
        }

        $clientUa = trim((string) ($eventPayload['client_user_agent'] ?? ''));
        if ($clientUa !== '') {
            $userData['client_user_agent'] = $clientUa;
        }

        $fbclid = trim((string) ($event?->fbclid ?? ''));
        if ($fbclid !== '') {
            $userData['fbc'] = 'fb.1.'.now()->timestamp.'.'.$fbclid;
        }

        $customData = [
            'currency' => (string) ($conversion->currency ?: 'TRY'),
        ];

        if ($conversion->value !== null) {
            $customData['value'] = (float) $conversion->value;
        }

        $leadType = trim((string) ($eventPayload['lead_type'] ?? ''));
        if ($leadType !== '') {
            $customData['content_name'] = $leadType;
        }

        $campaignName = trim((string) ($conversion->campaign?->name ?? ''));
        if ($campaignName !== '') {
            $customData['content_category'] = $campaignName;
        }

        return [
            'event_name' => $eventName,
            'event_time' => now()->timestamp,
            'event_id' => $eventId,
            'action_source' => 'website',
            'event_source_url' => $eventSourceUrl,
            'user_data' => $userData,
            'custom_data' => $customData,
        ];
    }

    private function mapEventName(string $eventName): string
    {
        return match (strtolower(trim($eventName))) {
            'lead_submitted', 'lead' => 'Lead',
            'contact_submitted', 'contact' => 'Contact',
            default => 'Lead',
        };
    }

    private function resolveConnection(AdConversion $conversion): ?AdConnection
    {
        $campaignConnection = $conversion->campaign?->connection;
        if ($campaignConnection instanceof AdConnection) {
            return $campaignConnection;
        }

        return AdConnection::query()
            ->where('platform', 'meta')
            ->whereIn('status', ['connected', 'draft'])
            ->latest('id')
            ->first();
    }

    private function resolvePixelId(AdConversion $conversion, ?AdConnection $connection): string
    {
        $campaignPixel = (string) data_get($conversion->campaign?->meta, 'pixel_id', '');
        if ($campaignPixel !== '') {
            return preg_replace('/\D+/', '', $campaignPixel) ?? '';
        }

        $connectionPixel = (string) data_get($connection?->meta, 'pixel_id', '');
        if ($connectionPixel !== '') {
            return preg_replace('/\D+/', '', $connectionPixel) ?? '';
        }

        $globalPixel = (string) config('adscore.meta.pixel_id', '');
        return preg_replace('/\D+/', '', $globalPixel) ?? '';
    }

    private function resolveAccessToken(?AdConnection $connection): string
    {
        $connectionToken = trim((string) ($connection?->access_token ?? ''));
        if ($connectionToken !== '') {
            return $connectionToken;
        }

        return trim((string) config('adscore.meta.access_token', ''));
    }

    private function isDirectModeEnabled(?AdConnection $connection, string $pixelId, string $accessToken): bool
    {
        return $this->resolveMetaEnabled($connection)
            && $pixelId !== ''
            && $accessToken !== '';
    }

    private function resolveMetaEnabled(?AdConnection $connection): bool
    {
        $default = (bool) config('adscore.meta.enabled', true);
        $configured = data_get($connection?->meta, 'capi_enabled', null);

        return $this->toBool($configured, $default);
    }

    private function resolveTestEventCode(?AdConnection $connection): string
    {
        $connectionTestCode = trim((string) data_get($connection?->meta, 'test_event_code', ''));
        if ($connectionTestCode !== '') {
            return $connectionTestCode;
        }

        return trim((string) config('adscore.meta.test_event_code', ''));
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

    private function resolveLead(?AdEvent $event): ?Lead
    {
        $leadId = (int) ($event?->lead_id ?? 0);
        if ($leadId <= 0) {
            return null;
        }

        return Lead::query()->find($leadId);
    }

    private function hashNormalized(?string $value): ?string
    {
        $normalized = strtolower(trim((string) $value));
        if ($normalized === '') {
            return null;
        }

        return hash('sha256', $normalized);
    }

    private function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        // TR local mobile: 0XXXXXXXXXX -> 90XXXXXXXXXX
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = '9'.$digits;
        }

        return $digits;
    }

    private function mockConversionResponse(AdConversion $conversion, string $reason): array
    {
        return [
            'success' => true,
            'status' => 'sent',
            'external_id' => $conversion->external_id ?: 'mconv-'.$conversion->id,
            'capi_event_id' => 'capi-'.$conversion->id,
            'channel' => 'meta_capi',
            'mode' => 'mock',
            'reason' => $reason,
        ];
    }

    public function fetchInsights(array $filters = []): array
    {
        return [
            'success' => true,
            'rows' => [],
            'filters' => $filters,
        ];
    }
}
