<?php

namespace App\Importers;

use App\Api\Exceptions\MyEvexiasRequestFailedException;
use App\Data\PracticeDatum;
use App\Models\Practice;
use App\Repositories\PracticeRepository;
use App\Services\PlacesService;
use App\Services\PracticeService;
use Exception;
use Generator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use NativeRank\InventorySync\Facades\InventorySync;

class PracticeImporter
{
    private const string LAST_IMPORT_KEY = 'myevexias-com-import:last-timestamp:practice-date-range';
    private const string IMPORT_NEXT_PAGE_KEY = 'myevexias-com-import:next-page:';
    private string $resource;
    private ?Carbon $lastImport;
    private array $processed = [];

    public function __construct(
        private readonly PracticeService $service,
        private readonly PracticeRepository $repository,
        private readonly PractitionerImporter $practitionerImporter,
        private readonly PlacesService $placesService,
    ) {
        $this->lastImport = Cache::get(self::LAST_IMPORT_KEY);

        if (is_null($this->lastImport)) {
            $this->resource = 'practice';
        } else {
            $this->resource = 'practice-date-range';   
        }
    }

    public function import(): void
    {
        try {
            $this->runImport();
        } catch (MyEvexiasRequestFailedException $exception) {
            if ($exception->resource === $this->resource) {
                Cache::put(self::IMPORT_NEXT_PAGE_KEY . $exception->resource, $exception->nextLink, now()->addMinutes(30));
            }

            throw $exception;
        }
    }

    public static function reset(): void
    {
        Cache::forget(self::LAST_IMPORT_KEY);
        Cache::forget(self::IMPORT_NEXT_PAGE_KEY . 'practice');
        Cache::forget(self::IMPORT_NEXT_PAGE_KEY . 'practice-date-range');
    }

    private function runImport(): void
    {
        $start = now();

        $generator = $this->generator();

        /** @var PracticeDatum $practiceDatum */
        foreach($generator as $practiceDatum) {
            if ($practiceDatum->name === 'NONE') {
                continue;
            }
            
            if (! $practiceDatum->active) {
                $this->repository->delete($practiceDatum);
                continue;
            }

            if (! $practiceDatum->visible) {
                $this->repository->delete($practiceDatum);
                continue;
            }

            $practice = null;
            InventorySync::withoutSyncing(function () use (&$practice, $practiceDatum) {
                $practice = $this->importPractice($practiceDatum);
            });
            /** @var Practice $practice */            
            $practice->save();
        } 

        if ($this->resource === 'practice') {
            $this->repository->cleanUp($this->processed);
        }

        Cache::put(self::LAST_IMPORT_KEY, $start);
        Cache::forget(self::IMPORT_NEXT_PAGE_KEY . $this->resource);
    }

    public function importPractice(PracticeDatum $practiceDatum): Practice
    {
        logger()->debug('importing practice', ['practice' => $practiceDatum->id, 'unique_name' => $practiceDatum->uniqueName]);

        $practice = $this->repository->save($practiceDatum);

        $this->locate($practice, $practiceDatum);
        $this->practitionerImporter->import($practice, $practiceDatum->practitioners);
        $this->processed[] = $practice->id;

        return $practice;
    }

    private function generator(): Generator
    {
     
        if ($this->resource === 'practice') {
            return $this->service->fetchAll(null);
        }

        if ($this->resource === 'practice-date-range') {
            return $this->service->fetchByDateRange($this->lastImport, now(), Cache::get(self::IMPORT_NEXT_PAGE_KEY . $this->resource));
        }

        throw new Exception('invalid resource ' . $this->resource);
    }

    

    private function locate(Practice $practice, PracticeDatum $practiceDatum): void
    {
        if (empty($practice->address)) {
            $practice->location()->delete();
            return;
        }

        if ($practice->wasChanged('address') || $practice->wasChanged('name')) {
            $practice->location()->delete();
        }

        if ($practice->location()->exists()) {
            return;
        }

        $place = $this->placesService->textSearch($practice->name . ' at ' . $practice->address);

        if (isset($place)) {
            $practice->location()->create([
                'place_id' => $place->placeId,
                'latitude' => $place->latitude,
                'longitude' => $place->longitude,
                'formatted_address' => $place->formattedAddress ?? $practice->address,
                'street_number' => $place->streetNumber,
                'route' => $place->route,
                'subpremise' => $place->subpremise,
                'locality' => $place->locality ?? $practiceDatum->city,
                'administrative_area_level_1' => $place->administrativeAreaLevel1 ?? $practiceDatum->state,
                'country' => $place->country,
                'postal_code' => $place->postalCode,
                'postal_code_suffix' => $place->postalCodeSuffix,
                'metadata' => [
                    'maps_uri' => $place->mapsUri,
                    'reviews' => $place->reviews,
                    'photos' => $place->photos,
                    'google_maps_links' => $place->googleMapsLinks,
                    'hours' => $place->hours,
                    'website_uri' => $place->websiteUri,
                ],
            ]);
        }
    }
}