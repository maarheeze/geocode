<?php

declare(strict_types=1);

namespace Tests\Feature;

use Faker\Factory;
use Maarheeze\Geocode\Distance;
use PHPUnit\Framework\TestCase;

class DistanceTest extends TestCase
{
    public function testAsKilometersConvertsFromMeters(): void
    {
        $distance = new Distance(1500.0);

        $this->assertSame(1.5, $distance->asKilometers());
    }

    public function testAsMetersReturnsTheGivenValue(): void
    {
        $faker = Factory::create();

        $meters = $faker->randomFloat();

        $distance = new Distance($meters);

        $this->assertSame($meters, $distance->asMeters());
    }
}
