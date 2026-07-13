<?php

declare(strict_types=1);

namespace Tests\Feature;

use Maarheeze\Geocode\Coordinates;

class CoordinatesTest extends FeatureTestCase
{
    public function testCalculatesDistanceBetweenTwoPoints(): void
    {
        $equator = new Coordinates(0.0, 0.0);
        $oneDegreeEast = new Coordinates(0.0, 1.0);

        $this->assertEqualsWithDelta(111194.93, $equator->distanceTo($oneDegreeEast)->asMeters(), 0.5);
    }

    public function testDistanceToSelfIsZero(): void
    {
        $coordinates = new Coordinates($this->faker->latitude(), $this->faker->longitude());

        $this->assertSame(0.0, $coordinates->distanceTo($coordinates)->asMeters());
    }
}
