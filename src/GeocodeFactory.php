<?php

declare(strict_types=1);

namespace Maarheeze\Geocode;

use GuzzleHttp\Client;
use Spatie\Geocoder\Geocoder;

class GeocodeFactory
{
    public static function create(string $apiKey): Geocode
    {
        $geocoder = new Geocoder(new Client());
        $geocoder->setApiKey($apiKey);

        return new SpatieGeocode($geocoder);
    }
}
