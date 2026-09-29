<?php

namespace App\Service;

use App\Entity\Partner;

final class PartnerNetwork
{
    /** Approximate country centres, used when no partner of the country has coordinates yet. */
    private const COUNTRY_CENTERS = [
        'AR' => [-34.0, -64.0], 'AU' => [-25.0, 134.0], 'BR' => [-14.0, -51.0], 'CA' => [56.0, -106.0],
        'CL' => [-33.0, -71.0], 'CN' => [35.0, 103.0], 'CO' => [4.0, -72.0], 'DE' => [51.0, 10.0],
        'ES' => [40.2, -3.7], 'FR' => [46.6, 2.4], 'GB' => [52.5, -1.5], 'IL' => [31.0, 35.0],
        'IN' => [21.0, 78.0], 'IT' => [42.8, 12.5], 'JP' => [36.0, 138.0], 'MX' => [23.0, -102.0],
        'NL' => [52.2, 5.3], 'NZ' => [-41.0, 174.0], 'PT' => [39.6, -8.0], 'UY' => [-32.5, -56.0],
        'US' => [39.0, -98.0], 'ZA' => [-29.0, 24.0],
    ];

    public function __construct(private readonly MapProjection $mapProjection)
    {
    }

    /**
     * Groups partners by country (Brazil first, then by number of institutions) with map coordinates.
     *
     * @param Partner[] $partners
     */
    public function byCountry(array $partners, string $locale): array
    {
        $countries = [];
        foreach ($partners as $partner) {
            $code = $partner->getCountry() ?: 'BR';
            $countries[$code] ??= [
                'code' => $code,
                'name' => $partner->getCountryName($locale),
                'partners' => [],
                'points' => [],
            ];
            $countries[$code]['partners'][] = $partner;
            if ($partner->getLatitude() !== null && $partner->getLongitude() !== null) {
                $countries[$code]['points'][] = $this->mapProjection->world((float) $partner->getLatitude(), (float) $partner->getLongitude());
            }
        }

        foreach ($countries as &$country) {
            $country['count'] = count($country['partners']);
            $country['point'] = $this->centroid($country['points']);
            if (!$country['point'] && isset(self::COUNTRY_CENTERS[$country['code']])) {
                [$lat, $lng] = self::COUNTRY_CENTERS[$country['code']];
                $country['point'] = $this->mapProjection->world($lat, $lng);
            }
            unset($country['points']);
        }
        unset($country);

        uasort($countries, fn (array $a, array $b) => [$b['code'] === 'BR', $b['count'], $a['name']] <=> [$a['code'] === 'BR', $a['count'], $b['name']]);

        return array_values($countries);
    }

    /**
     * Groups Brazilian partners by city with coordinates on the Brazil map.
     *
     * @param Partner[] $partners
     */
    public function brazilianCities(array $partners): array
    {
        $cities = [];
        foreach ($partners as $partner) {
            if (!$partner->isBrazilian() || $partner->getLatitude() === null || $partner->getLongitude() === null) {
                continue;
            }
            $city = $partner->getCity() ?: '—';
            $cities[$city] ??= ['name' => $city, 'partners' => [], 'points' => []];
            $cities[$city]['partners'][] = $partner;
            $cities[$city]['points'][] = $this->mapProjection->brazil((float) $partner->getLatitude(), (float) $partner->getLongitude());
        }

        foreach ($cities as &$city) {
            $city['count'] = count($city['partners']);
            $city['point'] = $this->centroid($city['points']);
            unset($city['points']);
        }
        unset($city);

        uasort($cities, fn (array $a, array $b) => [$b['count'], $a['name']] <=> [$a['count'], $b['name']]);

        return array_values($cities);
    }

    /** @param array<int, array{x: float, y: float}> $points */
    private function centroid(array $points): ?array
    {
        if (!$points) {
            return null;
        }

        return [
            'x' => round(array_sum(array_column($points, 'x')) / count($points), 1),
            'y' => round(array_sum(array_column($points, 'y')) / count($points), 1),
        ];
    }
}
