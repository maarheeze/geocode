# Geocode

**A tiny, typed geocoding wrapper.**

Turn an address string into latitude/longitude coordinates behind a single, clean interface—no array digging, no magic numbers, just a typed result or `null`. Pick a backend by name: the worldwide [Google Maps Geocoding API](https://developers.google.com/maps/documentation/geocoding), or the key-free Dutch [PDOK Locatieserver](https://www.pdok.nl/).


**Why use it?**
- Typed result: get a `Coordinates` object with `float $latitude` and `float $longitude`, not a loose array
- Interface-first: depend on the `Geocode` interface, swap implementations or mock it in tests
- Pluggable drivers: choose a backend by name when you build the factory
- No surprises: returns `null` when an address can't be resolved instead of `(0, 0)` coordinates

## Installation

```bash
composer require maarheeze/geocode
```

## Drivers

Select a driver by passing its name to `GeocodeFactory::create()`:

| Driver | Backend | Parameters | Coverage |
| --- | --- | --- | --- |
| `google` | [Google Maps Geocoding API](https://developers.google.com/maps/documentation/geocoding) | `apiKey` | Worldwide |
| `pdok` | [PDOK Locatieserver](https://www.pdok.nl/) | none | Netherlands only |

> **Note:** the `pdok` driver is **Netherlands-only** — it resolves Dutch addresses without any API key, but returns nothing for addresses elsewhere. It's a convenient key-free option when you only geocode Dutch addresses; the `google` driver covers the rest of the world but it needs an api-key.

## Usage

Build a `Geocode` instance with `GeocodeFactory::create()`, passing the driver name and its parameters. It wires up the underlying HTTP client and driver for you:

```php
use Maarheeze\Geocode\GeocodeFactory;

$geocode = GeocodeFactory::create('google', ['apiKey' => 'your-google-maps-api-key']);

$result = $geocode->getCoordinatesForAddress('Stationsstraat 1, Maarheeze');

if ($result === null) {
    return;
}

echo $result->latitude;
echo $result->longitude;
```

### Distance between two addresses

Geocode two addresses and measure the distance between them:

```php
use Maarheeze\Geocode\GeocodeFactory;

$geocode = GeocodeFactory::create('pdok');

$eindhoven = $geocode->getCoordinatesForAddress('Eindhoven');
$maarheeze = $geocode->getCoordinatesForAddress('Maarheeze');

if ($eindhoven === null || $maarheeze === null) {
    return;
}

echo $eindhoven->distanceTo($maarheeze)->asKilometers(); // e.g. 18.7
```

## API

### `Geocode::getCoordinatesForAddress(string $address): ?Coordinates`

Resolves an address to coordinates. Returns a `Coordinates` object on success, or `null` when the address can't be geocoded.

Throws `Maarheeze\Geocode\GeocodingFailed` when the lookup itself fails—a transport error or an error returned by the geocoding service (e.g. an invalid API key or exceeded quota). A `null` result means "not found"; an exception means "could not look up".

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
