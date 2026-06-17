# Geocode

**A tiny, typed wrapper around [spatie/geocoder](https://github.com/spatie/geocoder).**

Turn an address string into latitude/longitude coordinates behind a single, clean interface—no array digging, no magic numbers, just a typed result or `null`.

**Why use it?**
- 🎯 **Typed result** — Get a `Coordinates` object with `float $latitude` and `float $longitude`, not a loose array
- 🧩 **Interface-first** — Depend on the `Geocode` interface, swap implementations or mock it in tests
- 🚫 **No surprises** — Returns `null` when an address can't be resolved instead of `(0, 0)` coordinates

## Installation

```bash
composer require maarheeze/geocode
```

Requires PHP 8.3+.

## Usage

Build a `Geocode` instance with `GeocodeFactory`. It takes your Google Maps API key—nothing else—and wires up the underlying HTTP client and Spatie geocoder for you:

```php
use Maarheeze\Geocode\GeocodeFactory;

$geocode = GeocodeFactory::create('your-google-maps-api-key');

$result = $geocode->getCoordinatesForAddress('Stationsstraat 1, Maarheeze');

if ($result === null) {
    // Address could not be resolved.
    return;
}

echo $result->latitude;
echo $result->longitude;
```

### Distance between two addresses

Geocode two addresses and measure the distance between them:

```php
use Maarheeze\Geocode\GeocodeFactory;

$geocode = GeocodeFactory::create('your-google-maps-api-key');

$eindhoven = $geocode->getCoordinatesForAddress('Eindhoven');
$maarheeze = $geocode->getCoordinatesForAddress('Maarheeze');

if ($eindhoven === null || $maarheeze === null) {
    // One of the addresses could not be resolved.
    return;
}

echo $eindhoven->distanceTo($maarheeze)->asKilometers(); // e.g. 18.7
```

## API

### `Geocode::getCoordinatesForAddress(string $address): ?Coordinates`

Resolves an address to coordinates. Returns a `Coordinates` object on success, or `null` when the address can't be geocoded.

Throws `Maarheeze\Geocode\Exceptions\GeocodingFailed` when the lookup itself fails—a transport error or an error returned by the geocoding service (e.g. an invalid API key or exceeded quota). A `null` result means "not found"; an exception means "could not look up".

### `Coordinates`

A readonly value object holding the result:

- `float $latitude`
- `float $longitude`

#### `Coordinates::distanceTo(Coordinates $other): Distance`

Returns the great-circle distance to another point (Haversine) as a `Distance`.

### `Distance`

A readonly value object wrapping a distance, with unit-explicit accessors:

- `asMeters(): float`
- `asKilometers(): float`

## License

MIT
