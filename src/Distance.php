<?php

declare(strict_types=1);

namespace Maarheeze\Geocode;

readonly class Distance
{
    private const float METERS_PER_KILOMETER = 1_000;

    public function __construct(
        private float $meters,
    ) {
    }

    public function asKilometers(): float
    {
        return $this->meters / self::METERS_PER_KILOMETER;
    }

    public function asMeters(): float
    {
        return $this->meters;
    }
}
