<?php

namespace App\Service;

/**
 * Converts latitude/longitude into coordinates of the static SVG maps in templates/pub/_map.
 * The constants match the generation of those SVG files (Natural Earth I for the world, scaled
 * equirectangular for Brazil) and must be updated together if the maps are regenerated.
 */
final class MapProjection
{
    public const WORLD_WIDTH = 1000;
    public const WORLD_HEIGHT = 435.1;
    private const WORLD_SCALE = 185.01307598489572;
    private const WORLD_TX = 500.0;
    private const WORLD_TY = 255.08461989812517;

    public const BRAZIL_WIDTH = 500;
    public const BRAZIL_HEIGHT = 515.0;
    private const BRAZIL_K = 0.9659258262890683;
    private const BRAZIL_SCALE = 13.206207854065235;
    private const BRAZIL_MIN_X = -71.48048742963165;
    private const BRAZIL_MIN_Y = -5.257981;

    /** @return array{x: float, y: float} */
    public function world(float $latitude, float $longitude): array
    {
        $lambda = deg2rad($longitude);
        $phi = deg2rad($latitude);
        $phi2 = $phi * $phi;
        $phi4 = $phi2 * $phi2;

        $x = $lambda * (0.8707 - 0.131979 * $phi2 + $phi4 * (-0.013791 + $phi4 * (0.003971 * $phi2 - 0.001529 * $phi4)));
        $y = $phi * (1.007226 + $phi2 * (0.015085 + $phi4 * (-0.044475 + 0.028874 * $phi2 - 0.005916 * $phi4)));

        return [
            'x' => round($x * self::WORLD_SCALE + self::WORLD_TX, 1),
            'y' => round(self::WORLD_TY - $y * self::WORLD_SCALE, 1),
        ];
    }

    /** @return array{x: float, y: float} */
    public function brazil(float $latitude, float $longitude): array
    {
        return [
            'x' => round(($longitude * self::BRAZIL_K - self::BRAZIL_MIN_X) * self::BRAZIL_SCALE, 1),
            'y' => round((-$latitude - self::BRAZIL_MIN_Y) * self::BRAZIL_SCALE, 1),
        ];
    }
}
