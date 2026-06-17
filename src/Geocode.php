<?php

declare(strict_types=1);

namespace Maarheeze\Geocode;

use Maarheeze\Geocode\Exceptions\GeocodingFailed;

interface Geocode
{
    /**
     * @throws GeocodingFailed
     */
    public function getCoordinatesForAddress(string $address): ?Coordinates;
}
