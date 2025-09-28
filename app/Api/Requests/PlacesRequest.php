<?php

namespace App\Api\Requests;

final readonly class PlacesRequest
{
    public function __construct(
        public string $key,
        public string $address,
    ) {}

    public function query(): array
    {
        return [
            'key' => $this->key,
            'address' => $this->address,
        ];
    }

    public function body(): array
    {
        return [
            'textQuery' => $this->address,
        ];
    }
}