<?php

namespace App\Services;

use App\Api\GeocoderApi;
use App\Api\Requests\GeocodeRequest;
use App\Api\Responses\GeocodeResponse;
use Illuminate\Support\Facades\Cache;
use Throwable;

final readonly class GeocodingService
{
    public function __construct(
        private string $key,
        private GeocoderApi $api,
    ) {}

    public function geocodeAddress(string $address): ?GeocodeResponse
    {
        try {
            return $this->api->geocode(new GeocodeRequest($this->key, $address));
        } catch (Throwable $throwable) {
            logger()->error('geocoding api error', ['address' => $address, 'exception' => $throwable]);

            return $this->handleFailure($address);
        }
    }

    private function handleFailure(string $address): ?GeocodeResponse
    {
        $key = 'geocode:api_failures:' . hash('sha256', $address);

        if (! Cache::has($key)) {
            Cache::set($key, 1, now()->addUTCDays(7));

            return null;
        }

        $failures = Cache::increment($key);

        if ($failures >= 5) {
            logger()->warning('geocoding api failures exceeded threshold', ['address' => $address, 'failures' => $failures]);

            return new GeocodeResponse(null, null, '', '');
        }

        return null;
    }
}