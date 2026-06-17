<?php

declare(strict_types=1);

namespace Maarheeze\Geocode;

use Spatie\Geocoder\Geocoder;

readonly class SpatieGeocode implements Geocode
{
    public function __construct(
        private Geocoder $geocoder,
    ) {
    }

    public function getCoordinatesForAddress(string $address): ?Coordinates
    {
        $result = $this->geocoder->getCoordinatesForAddress($address);

        if ($result['accuracy'] === Geocoder::RESULT_NOT_FOUND) {
            return null;
        }

        return new Coordinates($result['lat'], $result['lng']);
    }
}
