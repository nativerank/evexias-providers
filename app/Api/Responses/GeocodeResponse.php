<?php

namespace App\Api\Responses;

class GeocodeResponse
{
    public function __construct(
        public float $latitude,
        public float $longitude,
        public string $placeId,
    ) {}
}