<?php

declare(strict_types=1);

namespace Maarheeze\Geocode;

interface Geocode
{
    public function getCoordinatesForAddress(string $address): ?Coordinates;
}
