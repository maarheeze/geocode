<?php

declare(strict_types=1);

namespace Tests\Feature\Drivers\Google;

use Maarheeze\Geocode\Coordinates;
use Maarheeze\Geocode\Drivers\Google\GoogleGeocode;
use Maarheeze\Geocode\GeocodingFailed;
use Spatie\Geocoder\Exceptions\CouldNotGeocode;
use Spatie\Geocoder\Geocoder;
use Tests\Feature\FeatureTestCase;

class GoogleGeocodeTest extends FeatureTestCase
{
    public function testReturnsCoordinatesForResolvedAddress(): void
    {
        $latitude = $this->faker->latitude();
        $longitude = $this->faker->longitude();
        $address = $this->faker->city();

        $geocoder = $this->createMock(Geocoder::class);
        $geocoder->expects($this->once())
            ->method('getCoordinatesForAddress')
            ->with($address)
            ->willReturn([
                'lat' => $latitude,
                'lng' => $longitude,
                'accuracy' => $this->faker->randomElement([
                    'ROOFTOP',
                    'RANGE_INTERPOLATED',
                    'GEOMETRIC_CENTER',
                    'APPROXIMATE',
                ]),
                'formatted_address' => $this->faker->address(),
            ]);

        $service = new GoogleGeocode($geocoder);

        $result = $service->getCoordinatesForAddress($address);

        $this->assertInstanceOf(Coordinates::class, $result);
        $this->assertSame($latitude, $result->latitude);
        $this->assertSame($longitude, $result->longitude);
    }

    public function testReturnsNullWhenAddressCannotBeResolved(): void
    {
        $address = $this->faker->city();

        $geocoder = $this->createMock(Geocoder::class);
        $geocoder->expects($this->once())
            ->method('getCoordinatesForAddress')
            ->with($address)
            ->willReturn([
                'lat' => $this->faker->latitude(),
                'lng' => $this->faker->longitude(),
                'accuracy' => Geocoder::RESULT_NOT_FOUND,
                'formatted_address' => Geocoder::RESULT_NOT_FOUND,
            ]);

        $service = new GoogleGeocode($geocoder);

        $result = $service->getCoordinatesForAddress($address);

        $this->assertNull($result);
    }

    public function testWrapsGeocoderFailuresInGeocodingFailed(): void
    {
        $address = $this->faker->city();

        $geocoder = $this->createMock(Geocoder::class);
        $geocoder->expects($this->once())
            ->method('getCoordinatesForAddress')
            ->with($address)
            ->willThrowException(new CouldNotGeocode('Could not connect to googleapis.com/maps/api'));

        $service = new GoogleGeocode($geocoder);

        $this->expectException(GeocodingFailed::class);

        $service->getCoordinatesForAddress($address);
    }
}
