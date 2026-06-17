<?php

declare(strict_types=1);

namespace Maarheeze\Geocode;

use GuzzleHttp\Exception\GuzzleException;
use Maarheeze\Geocode\Exceptions\GeocodingFailed;
use Spatie\Geocoder\Exceptions\CouldNotGeocode;
use Spatie\Geocoder\Geocoder;

readonly class SpatieGeocode implements Geocode
{
    public function __construct(
        private Geocoder $geocoder,
    ) {
    }

    public function getCoordinatesForAddress(string $address): ?Coordinates
    {
        try {
            $result = $this->geocoder->getCoordinatesForAddress($address);
        } catch (CouldNotGeocode | GuzzleException $exception) {
            throw new GeocodingFailed('Geocoding failed', $exception->getCode(), $exception);
        }

        if ($result['accuracy'] === Geocoder::RESULT_NOT_FOUND) {
            return null;
        }

        return new Coordinates($result['lat'], $result['lng']);
    }
}
