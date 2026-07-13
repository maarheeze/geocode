<?php

declare(strict_types=1);

namespace Tests\Feature;

use Maarheeze\Geocode\Distance;

class DistanceTest extends FeatureTestCase
{
    public function testAsKilometersConvertsFromMeters(): void
    {
        $distance = new Distance(1500.0);

        $this->assertSame(1.5, $distance->asKilometers());
    }

    public function testAsMetersReturnsTheGivenValue(): void
    {
        $meters = $this->faker->randomFloat();

        $distance = new Distance($meters);

        $this->assertSame($meters, $distance->asMeters());
    }
}
