<?php

namespace App\Api\Responses;

class GeocodeResponse
{
    public function __construct(
        public ?float $latitude = null,
        public ?float $longitude = null,
        public string $formattedAddress,
        public string $placeId,
        public ?string $subpremise = null,
        public ?string $streetNumber = null,
        public ?string $route = null,
        public ?string $administrativeAreaLevel1 = null,
        public ?string $country = null,
        public ?string $postalCode = null,
        public ?string $postalCodeSuffix = null,
    ) {}

    public static function parse(array $result): self
    {
        return new self(
            latitude: $result['geometry']['location']['lat'],
            longitude: $result['geometry']['location']['lng'],
            formattedAddress: $result['formatted_address'],
            placeId: $result['place_id'],
            subpremise: self::extractAddressComponent($result['address_components'], 'subpremise'),
            streetNumber: self::extractAddressComponent($result['address_components'], 'street_number'),
            route: self::extractAddressComponent($result['address_components'], 'route'),
            administrativeAreaLevel1: self::extractAddressComponent($result['address_components'], 'administrative_area_level_1'),
            country: self::extractAddressComponent($result['address_components'], 'country'),
            postalCode: self::extractAddressComponent($result['address_components'], 'postal_code'),
            postalCodeSuffix: self::extractAddressComponent($result['address_components'], 'postal_code_suffix'),
        );
    }

    private static function extractAddressComponent(array $components, string $type): ?string
    {
        foreach ($components as $component) {
            if (in_array($type, $component['types'], true)) {
                return $component['short_name'];
            }
        }

        return null;
    }
}