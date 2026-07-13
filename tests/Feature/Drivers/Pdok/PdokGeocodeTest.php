<?php

declare(strict_types=1);

namespace Tests\Feature\Drivers\Pdok;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use Maarheeze\Geocode\Coordinates;
use Maarheeze\Geocode\Drivers\Pdok\PdokGeocode;
use Maarheeze\Geocode\GeocodingFailed;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\FeatureTestCase;

use function json_encode;

use const JSON_THROW_ON_ERROR;

class PdokGeocodeTest extends FeatureTestCase
{
    public function testReturnsCoordinatesForResolvedAddress(): void
    {
        $address = $this->faker->city();
        $latitude = 51.31038882;
        $longitude = 5.61170886;

        $body = json_encode([
            'response' => [
                'docs' => [
                    ['centroide_ll' => 'POINT(5.61170886 51.31038882)'],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], $body));

        $service = new PdokGeocode($client);

        $result = $service->getCoordinatesForAddress($address);

        $this->assertInstanceOf(Coordinates::class, $result);
        $this->assertSame($latitude, $result->latitude);
        $this->assertSame($longitude, $result->longitude);
    }

    public function testReturnsNullWhenAddressCannotBeResolved(): void
    {
        $address = $this->faker->city();

        $body = json_encode([
            'response' => [
                'docs' => [],
            ],
        ], JSON_THROW_ON_ERROR);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], $body));

        $service = new PdokGeocode($client);

        $result = $service->getCoordinatesForAddress($address);

        $this->assertNull($result);
    }

    public function testWrapsTransportFailuresInGeocodingFailed(): void
    {
        $address = $this->faker->city();

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->willThrowException(new TransferException('Could not connect'));

        $service = new PdokGeocode($client);

        $this->expectException(GeocodingFailed::class);

        $service->getCoordinatesForAddress($address);
    }

    #[DataProvider('malformedResponseProvider')]
    public function testThrowsGeocodingFailedForMalformedResponse(string $body): void
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, [], $body));

        $service = new PdokGeocode($client);

        $this->expectException(GeocodingFailed::class);

        $service->getCoordinatesForAddress($this->faker->city());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function malformedResponseProvider(): array
    {
        return [
            'body is not an array' => ['"not an object"'],
            'missing response key' => ['{"foo": "bar"}'],
            'response is not an array' => ['{"response": "not an array"}'],
            'missing docs key' => ['{"response": {}}'],
            'docs is not an array' => ['{"response": {"docs": "not an array"}}'],
            'missing centroide_ll key' => ['{"response": {"docs": [{"foo": "bar"}]}}'],
            'centroide_ll is not a string' => ['{"response": {"docs": [{"centroide_ll": 123}]}}'],
            'centroide_ll is not parsable' => ['{"response": {"docs": [{"centroide_ll": "LINESTRING(1 2)"}]}}'],
        ];
    }
}
