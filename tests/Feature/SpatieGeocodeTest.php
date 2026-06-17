<?php

declare(strict_types=1);

namespace Tests\Feature;

use Faker\Factory;
use Maarheeze\Geocode\Coordinates;
use Maarheeze\Geocode\Exceptions\GeocodingFailed;
use Maarheeze\Geocode\SpatieGeocode;
use PHPUnit\Framework\TestCase;
use Spatie\Geocoder\Exceptions\CouldNotGeocode;
use Spatie\Geocoder\Geocoder;

class SpatieGeocodeTest extends TestCase
{
    public function testReturnsCoordinatesForResolvedAddress(): void
    {
        $faker = Factory::create();

        $latitude = $faker->latitude();
        $longitude = $faker->longitude();
        $address = $faker->city();

        $geocoder = $this->createMock(Geocoder::class);
        $geocoder->expects($this->once())
            ->method('getCoordinatesForAddress')
            ->with($address)
            ->willReturn([
                'lat' => $latitude,
                'lng' => $longitude,
                'accuracy' => $faker->randomElement([
                    'ROOFTOP',
                    'RANGE_INTERPOLATED',
                    'GEOMETRIC_CENTER',
                    'APPROXIMATE',
                ]),
                'formatted_address' => $faker->address(),
            ]);

        $service = new SpatieGeocode($geocoder);

        $result = $service->getCoordinatesForAddress($address);

        $this->assertInstanceOf(Coordinates::class, $result);
        $this->assertSame($latitude, $result->latitude);
        $this->assertSame($longitude, $result->longitude);
    }

    public function testReturnsNullWhenAddressCannotBeResolved(): void
    {
        $faker = Factory::create();

        $address = $faker->city();

        $geocoder = $this->createMock(Geocoder::class);
        $geocoder->expects($this->once())
            ->method('getCoordinatesForAddress')
            ->with($address)
            ->willReturn([
                'lat' => $faker->latitude(),
                'lng' => $faker->longitude(),
                'accuracy' => Geocoder::RESULT_NOT_FOUND,
                'formatted_address' => Geocoder::RESULT_NOT_FOUND,
            ]);

        $service = new SpatieGeocode($geocoder);

        $result = $service->getCoordinatesForAddress($address);

        $this->assertNull($result);
    }

    public function testWrapsGeocoderFailuresInGeocodingFailed(): void
    {
        $faker = Factory::create();

        $address = $faker->city();

        $geocoder = $this->createMock(Geocoder::class);
        $geocoder->expects($this->once())
            ->method('getCoordinatesForAddress')
            ->with($address)
            ->willThrowException(CouldNotGeocode::couldNotConnect());

        $service = new SpatieGeocode($geocoder);

        $this->expectException(GeocodingFailed::class);

        $service->getCoordinatesForAddress($address);
    }
}
