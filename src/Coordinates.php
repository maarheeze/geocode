<?php

declare(strict_types=1);

namespace Maarheeze\Geocode;

use function asin;
use function cos;
use function deg2rad;
use function sin;
use function sqrt;

readonly class Coordinates
{
    private const float EARTH_RADIUS_METERS = 6_371_000.0;

    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
    }

    public function distanceTo(self $other): Distance
    {
        $latitudeFrom = deg2rad($this->latitude);
        $latitudeTo = deg2rad($other->latitude);
        $latitudeRad = deg2rad($other->latitude - $this->latitude);
        $longitudeRad = deg2rad($other->longitude - $this->longitude);

        $haversine = sin($latitudeRad / 2) ** 2 + cos($latitudeFrom) * cos($latitudeTo) * sin($longitudeRad / 2) ** 2;

        $meters = self::EARTH_RADIUS_METERS * 2 * asin(sqrt($haversine));

        return new Distance($meters);
    }
}
