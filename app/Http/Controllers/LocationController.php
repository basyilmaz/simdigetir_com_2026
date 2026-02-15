<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Tüm ilçeler listesi (/kurye)
     */
    public function allDistricts()
    {
        $locations = config('istanbul-locations');

        // İlçeleri yakaya göre grupla
        $avrupa = [];
        $anadolu = [];

        foreach ($locations as $slug => $district) {
            $district['slug'] = $slug;
            $district['neighborhood_count'] = count($district['neighborhoods'] ?? []);

            if ($district['side'] === 'avrupa') {
                $avrupa[$slug] = $district;
            } else {
                $anadolu[$slug] = $district;
            }
        }

        // Alfabetik sırala
        uasort($avrupa, fn($a, $b) => strcmp($a['name'], $b['name']));
        uasort($anadolu, fn($a, $b) => strcmp($a['name'], $b['name']));

        return view('landing.location-index', [
            'avrupa' => $avrupa,
            'anadolu' => $anadolu,
            'totalDistricts' => count($locations),
            'totalNeighborhoods' => collect($locations)->sum(fn($d) => count($d['neighborhoods'] ?? [])),
        ]);
    }

    /**
     * İlçe detay sayfası (/kurye/{district})
     */
    public function district(string $slug)
    {
        $locations = config('istanbul-locations');

        if (!isset($locations[$slug])) {
            abort(404);
        }

        $district = $locations[$slug];
        $district['slug'] = $slug;

        // Komşu ilçeleri bul (aynı yakadaki ilçeler)
        $neighbors = collect($locations)
            ->filter(fn($d, $s) => $d['side'] === $district['side'] && $s !== $slug)
            ->take(6)
            ->map(function ($d, $s) {
                $d['slug'] = $s;
                return $d;
            });

        return view('landing.location-district', [
            'district' => $district,
            'slug' => $slug,
            'neighbors' => $neighbors,
        ]);
    }

    /**
     * Mahalle detay sayfası (/kurye/{district}/{neighborhood})
     */
    public function neighborhood(string $districtSlug, string $neighborhoodSlug)
    {
        $locations = config('istanbul-locations');

        if (!isset($locations[$districtSlug])) {
            abort(404);
        }

        $district = $locations[$districtSlug];
        $district['slug'] = $districtSlug;

        if (!isset($district['neighborhoods'][$neighborhoodSlug])) {
            abort(404);
        }

        $neighborhoodName = $district['neighborhoods'][$neighborhoodSlug];

        // Aynı ilçedeki diğer mahalleler
        $otherNeighborhoods = collect($district['neighborhoods'])
            ->filter(fn($name, $slug) => $slug !== $neighborhoodSlug)
            ->take(8);

        return view('landing.location-neighborhood', [
            'district' => $district,
            'districtSlug' => $districtSlug,
            'neighborhoodSlug' => $neighborhoodSlug,
            'neighborhoodName' => $neighborhoodName,
            'otherNeighborhoods' => $otherNeighborhoods,
        ]);
    }
}
