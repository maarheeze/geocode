<?php

declare(strict_types=1);

namespace Tests\Feature;

use Maarheeze\Geocode\Drivers\Google\GoogleGeocode;
use Maarheeze\Geocode\Drivers\Pdok\PdokGeocode;
use Maarheeze\Geocode\GeocodeFactory;
use Maarheeze\Geocode\InvalidGeocodeDriver;
use Maarheeze\Geocode\MissingApiKey;

class GeocodeFactoryTest extends FeatureTestCase
{
    public function testCreatesGoogleGeocodeWhenDriverIsGoogle(): void
    {
        $geocode = GeocodeFactory::create('google', ['apiKey' => $this->faker->word()]);

        $this->assertInstanceOf(GoogleGeocode::class, $geocode);
    }

    public function testCreatesPdokGeocodeWhenDriverIsPdok(): void
    {
        $this->assertInstanceOf(PdokGeocode::class, GeocodeFactory::create('pdok'));
    }

    public function testThrowsWhenDriverIsInvalid(): void
    {
        $this->expectException(InvalidGeocodeDriver::class);

        GeocodeFactory::create($this->faker->word());
    }

    public function testThrowsWhenGoogleApiKeyIsMissing(): void
    {
        $this->expectException(MissingApiKey::class);

        GeocodeFactory::create('google');
    }
}
