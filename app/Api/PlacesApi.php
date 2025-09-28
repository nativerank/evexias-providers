<?php

namespace App\Api;

use App\Api\Requests\PlacesRequest;
use App\Api\Responses\PlaceResult;
use Exception;
use Illuminate\Support\Facades\Http;

final readonly class PlacesApi
{
    public function geocode(PlacesRequest $request): PlaceResult
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', $request->query());

        $response->throw();

        $results = $response->json('results', []);

        if (count($results) === 0) {
            throw new Exception('no results from geocoding api');
        }

        return PlaceResult::parseGeocodingResult($results[0]);
    }

    public function textSearch(PlacesRequest $request): PlaceResult
    {
        $response = Http::withHeaders([
            'X-Goog-Api-Key' => $request->key,
            'X-Goog-FieldMask' => implode(',', [
                'places.formattedAddress',
                'places.displayName',
                'places.location',
                'places.id',
                'places.addressComponents',
                'places.photos',
                'places.reviews',
                'places.reviewSummary',
                'places.containingPlaces',
                'places.googleMapsLinks',
                'places.googleMapsUri',
            ]),
        ])->post('https://places.googleapis.com/v1/places:searchText', $request->body());

        $response->throw();

        $results = $response->json('places', []);

        if (count($results) === 0) {
            throw new Exception('no results from places api');
        }

        return PlaceResult::parseTextSearchResult($results[0]);
    }
}