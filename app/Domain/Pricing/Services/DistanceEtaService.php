<?php

namespace App\Domain\Pricing\Services;

use Illuminate\Support\Facades\Http;

class DistanceEtaService
{
    /**
     * @return array{distance_meters:int,duration_seconds:int,source:string}|null
     */
    public function estimate(?float $pickupLat, ?float $pickupLng, ?float $dropoffLat, ?float $dropoffLng): ?array
    {
        if ($pickupLat === null || $pickupLng === null || $dropoffLat === null || $dropoffLng === null) {
            return null;
        }

        $apiKey = (string) config('services_integrations.maps.google_maps_api_key', '');
        if ($apiKey !== '') {
            $remote = $this->fromGoogle($pickupLat, $pickupLng, $dropoffLat, $dropoffLng, $apiKey);
            if ($remote !== null) {
                return $remote;
            }
        }

        return $this->fromHaversine($pickupLat, $pickupLng, $dropoffLat, $dropoffLng);
    }

    /**
     * @return array{distance_meters:int,duration_seconds:int,source:string}|null
     */
    private function fromGoogle(float $pickupLat, float $pickupLng, float $dropoffLat, float $dropoffLng, string $apiKey): ?array
    {
        $response = Http::timeout(8)->get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $pickupLat.','.$pickupLng,
            'destinations' => $dropoffLat.','.$dropoffLng,
            'mode' => 'driving',
            'language' => 'tr',
            'key' => $apiKey,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $json = (array) $response->json();
        $element = $json['rows'][0]['elements'][0] ?? null;
        if (! is_array($element) || (string) ($element['status'] ?? '') !== 'OK') {
            return null;
        }

        $distance = (int) ($element['distance']['value'] ?? 0);
        $duration = (int) ($element['duration']['value'] ?? 0);
        if ($distance <= 0 || $duration <= 0) {
            return null;
        }

        return [
            'distance_meters' => $distance,
            'duration_seconds' => $duration,
            'source' => 'google_maps',
        ];
    }

    /**
     * @return array{distance_meters:int,duration_seconds:int,source:string}
     */
    private function fromHaversine(float $pickupLat, float $pickupLng, float $dropoffLat, float $dropoffLng): array
    {
        $earthRadius = 6371000.0;
        $latFrom = deg2rad($pickupLat);
        $latTo = deg2rad($dropoffLat);
        $latDelta = deg2rad($dropoffLat - $pickupLat);
        $lngDelta = deg2rad($dropoffLng - $pickupLng);

        $a = sin($latDelta / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(max(0.0, 1 - $a)));
        $distance = (int) round($earthRadius * $c);

        // Conservative city-speed estimate (30 km/h).
        $duration = (int) round(max(60, ($distance / 30000) * 3600));

        return [
            'distance_meters' => max(0, $distance),
            'duration_seconds' => max(60, $duration),
            'source' => 'haversine_fallback',
        ];
    }
}

