<?php

declare(strict_types=1);

namespace Maarheeze\Geocode;

interface Geocode
{
    /**
     * @throws GeocodingFailed
     */
    public function getCoordinatesForAddress(string $address): ?Coordinates;
}
