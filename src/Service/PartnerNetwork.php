<?php

namespace App\Service;

use App\Entity\Partner;

final class PartnerNetwork
{
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
