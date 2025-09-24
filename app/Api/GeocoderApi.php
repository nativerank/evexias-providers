<?php

namespace App\Api;

use App\Api\Requests\GeocodeRequest;
use App\Api\Responses\GeocodeResponse;
use Exception;
use Illuminate\Support\Facades\Http;

final readonly class GeocoderApi
{
    public function geocode(GeocodeRequest $request): GeocodeResponse
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', $request->query());

        $response->throw();

        $results = $response->json('results', []);

        if (count($results) === 0) {
            throw new Exception('no results from geocoding api');
        }

        $firstResult = $results[0];

        $location = $firstResult['geometry']['location'];

        return new GeocodeResponse(
            latitude: $location['lat'],
            longitude: $location['lng'],
            placeId: $firstResult['place_id'],
        );
    }

}