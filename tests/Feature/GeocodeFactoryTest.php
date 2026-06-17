<?php

declare(strict_types=1);

namespace Tests\Feature;

use Faker\Factory;
use Maarheeze\Geocode\Geocode;
use Maarheeze\Geocode\GeocodeFactory;
use PHPUnit\Framework\TestCase;

class GeocodeFactoryTest extends TestCase
{
    public function testCreatesAGeocodeInstance(): void
    {
        $faker = Factory::create();

        $this->assertInstanceOf(Geocode::class, GeocodeFactory::create($faker->word()));
    }
}
