<?php

declare(strict_types=1);

namespace Maarheeze\Geocode;

use GuzzleHttp\Client;
use Maarheeze\Geocode\Drivers\Google\GoogleGeocode;
use Maarheeze\Geocode\Drivers\Pdok\PdokGeocode;
use Spatie\Geocoder\Geocoder;

use function array_key_exists;

class GeocodeFactory
{
    /**
     * @param array<string, string> $parameters
     */
    public static function create(string $driver, array $parameters = []): Geocode
    {
        return match ($driver) {
            'google' => self::createGoogleGeocode($parameters),
            'pdok' => self::createPdokGeocode(),
            default => throw new InvalidGeocodeDriver('Invalid geocode driver (unknown driver name)'),
        };
    }

    /**
     * @param array<string, string> $parameters
     */
    private static function createGoogleGeocode(array $parameters): GoogleGeocode
    {
        if (array_key_exists('apiKey', $parameters) === false) {
            throw new MissingApiKey('Google Maps API key is not configured (missing apiKey parameter)');
        }

        $apiKey = $parameters['apiKey'];

        $geocoder = new Geocoder(new Client());
        $geocoder->setApiKey($apiKey);

        return new GoogleGeocode($geocoder);
    }

    private static function createPdokGeocode(): PdokGeocode
    {
        return new PdokGeocode(new Client());
    }
}
