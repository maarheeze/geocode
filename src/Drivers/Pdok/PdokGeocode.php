<?php

declare(strict_types=1);

namespace Maarheeze\Geocode\Drivers\Pdok;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Maarheeze\Geocode\Coordinates;
use Maarheeze\Geocode\Geocode;
use Maarheeze\Geocode\GeocodingFailed;

use function array_key_exists;
use function is_array;
use function is_string;
use function json_decode;
use function preg_match;
use function reset;

readonly class PdokGeocode implements Geocode
{
    private const string ENDPOINT = 'https://api.pdok.nl/bzk/locatieserver/search/v3_1/free';

    public function __construct(
        private Client $client,
    ) {
    }

    /**
     * @throws GeocodingFailed
     */
    public function getCoordinatesForAddress(string $address): ?Coordinates
    {
        try {
            $response = $this->client->request('GET', self::ENDPOINT, [
                'query' => [
                    'q' => $address,
                    'fl' => 'centroide_ll',
                    'rows' => 1,
                ],
            ]);
        } catch (GuzzleException $exception) {
            throw new GeocodingFailed('Geocoding failed (request error)', $exception->getCode(), $exception);
        }

        $point = $this->extractPoint(json_decode((string) $response->getBody(), true));

        if ($point === null) {
            return null;
        }

        return $this->parsePoint($point);
    }

    /**
     * @throws GeocodingFailed
     */
    private function extractPoint(mixed $decoded): ?string
    {
        if (is_array($decoded) === false) {
            throw new GeocodingFailed('Geocoding failed (invalid JSON response)');
        }

        if (array_key_exists('response', $decoded) === false) {
            throw new GeocodingFailed('Geocoding failed (missing response)');
        }

        $result = $decoded['response'];

        if (is_array($result) === false) {
            throw new GeocodingFailed('Geocoding failed (invalid response)');
        }

        if (array_key_exists('docs', $result) === false) {
            throw new GeocodingFailed('Geocoding failed (missing docs)');
        }

        $documents = $result['docs'];

        if (is_array($documents) === false) {
            throw new GeocodingFailed('Geocoding failed (invalid docs)');
        }

        $document = reset($documents);

        if (is_array($document) === false) {
            return null;
        }

        if (array_key_exists('centroide_ll', $document) === false) {
            throw new GeocodingFailed('Geocoding failed (missing centroide_ll)');
        }

        $point = $document['centroide_ll'];

        if (is_string($point) === false) {
            throw new GeocodingFailed('Geocoding failed (invalid centroide_ll)');
        }

        return $point;
    }

    /**
     * @throws GeocodingFailed
     */
    private function parsePoint(string $point): Coordinates
    {
        if (preg_match('/^POINT\(([0-9.-]+) ([0-9.-]+)\)$/', $point, $matches) !== 1) {
            throw new GeocodingFailed('Geocoding failed (unparsable point)');
        }

        return new Coordinates((float) $matches[2], (float) $matches[1]);
    }
}
