<?php

namespace App\Api\Responses;

use Illuminate\Support\Arr;

class PlaceResult
{
    public function __construct(
        public ?float $latitude,
        public ?float $longitude,
        public string $formattedAddress,
        public string $placeId,
        public ?string $subpremise = null,
        public ?string $streetNumber = null,
        public ?string $route = null,
        public ?string $locality = null,
        public ?string $administrativeAreaLevel1 = null,
        public ?string $country = null,
        public ?string $postalCode = null,
        public ?string $postalCodeSuffix = null,
        public ?string $mapsUri = null,
        public array $googleMapsLinks = [],
        public array $reviews = [],
        public array $photos = [],
    ) {}

    public static function parseGeocodingResult(array $result): self
    {
        return new self(
            latitude: $result['geometry']['location']['lat'],
            longitude: $result['geometry']['location']['lng'],
            formattedAddress: $result['formatted_address'],
            placeId: $result['place_id'],
            subpremise: self::extractAddressComponent($result['address_components'], 'subpremise'),
            streetNumber: self::extractAddressComponent($result['address_components'], 'street_number'),
            route: self::extractAddressComponent($result['address_components'], 'route'),
            locality: self::extractAddressComponent($result['address_components'], 'locality'),
            administrativeAreaLevel1: self::extractAddressComponent($result['address_components'], 'administrative_area_level_1'),
            country: self::extractAddressComponent($result['address_components'], 'country'),
            postalCode: self::extractAddressComponent($result['address_components'], 'postal_code'),
            postalCodeSuffix: self::extractAddressComponent($result['address_components'], 'postal_code_suffix'),
        );
    }

    public static function parseTextSearchResult(array $result): self
    {
        return new self(
            latitude: $result['location']['latitude'],
            longitude: $result['location']['longitude'],
            formattedAddress: $result['formattedAddress'] ?? '',
            placeId: $result['id'] ?? '',
            subpremise: self::extractAddressComponent($result['addressComponents'], 'subpremise'),
            streetNumber: self::extractAddressComponent($result['addressComponents'], 'street_number'),
            route: self::extractAddressComponent($result['addressComponents'], 'route'),
            locality: self::extractAddressComponent($result['addressComponents'], 'locality'),
            administrativeAreaLevel1: self::extractAddressComponent($result['addressComponents'], 'administrative_area_level_1'),
            country: self::extractAddressComponent($result['addressComponents'], 'country'),
            postalCode: self::extractAddressComponent($result['addressComponents'], 'postal_code'),
            postalCodeSuffix: self::extractAddressComponent($result['addressComponents'], 'postal_code_suffix'),
            mapsUri: $result['googleMapsUri'] ?? null,
            googleMapsLinks: $result['googleMapsLinks'] ?? [],
            reviews: array_filter($result['reviews'] ?? [], fn($review) => $review['rating'] === 5),
            photos: Arr::take($result['photos'] ?? [], 5),
        );
    }

    private static function extractAddressComponent(array $components, string $type): ?string
    {
        foreach ($components as $component) {
            if (in_array($type, $component['types'], true)) {
                return $component['short_name'] ?? $component['shortText'];
            }
        }

        return null;
    }
}