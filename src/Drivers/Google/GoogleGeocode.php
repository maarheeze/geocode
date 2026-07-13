<?php

declare(strict_types=1);

namespace Maarheeze\Geocode\Drivers\Google;

use GuzzleHttp\Exception\GuzzleException;
use Maarheeze\Geocode\Coordinates;
use Maarheeze\Geocode\Geocode;
use Maarheeze\Geocode\GeocodingFailed;
use Spatie\Geocoder\Exceptions\CouldNotGeocode;
use Spatie\Geocoder\Geocoder;

readonly class GoogleGeocode implements Geocode
{
    public function __construct(
        private Geocoder $geocoder,
    ) {
    }

    /**
     * @throws GeocodingFailed
     */
    public function getCoordinatesForAddress(string $address): ?Coordinates
    {
        try {
            $result = $this->geocoder->getCoordinatesForAddress($address);
        } catch (CouldNotGeocode | GuzzleException $exception) {
            throw new GeocodingFailed('Geocoding failed (request error)', $exception->getCode(), $exception);
        }

        if ($result['accuracy'] === Geocoder::RESULT_NOT_FOUND) {
            return null;
        }

        return new Coordinates($result['lat'], $result['lng']);
    }
}
