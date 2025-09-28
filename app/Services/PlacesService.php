<?php

namespace App\Services;

use App\Api\GeocoderApi;
use App\Api\PlacesApi;
use App\Api\Requests\GeocodeRequest;
use App\Api\Requests\PlacesRequest;
use App\Api\Responses\GeocodeResponse;
use App\Api\Responses\PlaceResult;
use App\Api\Responses\PlacesResponse;
use Illuminate\Support\Facades\Cache;
use Throwable;

final readonly class PlacesService
{
    public function __construct(
        private string $key,
        private PlacesApi $api,
    ) {}

    public function geocodeAddress(string $search): ?PlaceResult
    {
        try {
            return $this->api->geocode(new PlacesRequest($this->key, $search));
        } catch (Throwable $throwable) {
            logger()->error('geocoding api error', ['address' => $search, 'exception' => $throwable]);

            return $this->handleFailure($search);
        }
    }

    public function textSearch(string $search): ?PlaceResult
    {
        try {
            return $this->api->textSearch(new PlacesRequest($this->key, $search));
        } catch (Throwable $throwable) {
            logger()->error('places api error', ['search' => $search, 'exception' => $throwable]);

            return $this->handleFailure($search);
        }
    }

    private function handleFailure(string $search): ?PlaceResult
    {
        $key = 'geocode:api_failures:' . hash('sha256', $search);

        if (! Cache::has($key)) {
            Cache::set($key, 1, now()->addUTCDays(7));

            return null;
        }

        $failures = Cache::increment($key);

        if ($failures >= 5) {
            logger()->warning('geocoding api failures exceeded threshold', ['address' => $search, 'failures' => $failures]);

            return new PlaceResult(null, null, '', '');
        }

        return null;
    }
}