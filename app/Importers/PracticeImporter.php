<?php

namespace App\Importers;

use App\Api\Exceptions\MyEvexiasRequestFailedException;
use App\Api\Responses\GeocodeResponse;
use App\Data\PracticeDatum;
use App\Models\Practice;
use App\Repositories\PracticeRepository;
use App\Services\GeocodingService;
use App\Services\PracticeService;
use Exception;
use Generator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Throwable;

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
        private readonly GeocodingService $geocodingService,
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
            if (! $practiceDatum->active) {
                $this->repository->delete($practiceDatum);
                continue;
            }

            if (! $practiceDatum->visible) {
                $this->repository->delete($practiceDatum);
                continue;
            }

            logger()->debug('importing practice', ['practice' => $practiceDatum->id]);

            $practice = $this->repository->save($practiceDatum);

            $this->geocode($practice);
            $this->practitionerImporter->import($practice, $practiceDatum->practitioners);
            $this->processed[] = $practice->id;
        }

        if ($this->resource === 'practice') {
            $this->repository->cleanUp($this->processed);
        }

        Cache::put(self::LAST_IMPORT_KEY, $start);
        Cache::forget(self::IMPORT_NEXT_PAGE_KEY . $this->resource);
    }

    private function generator(): Generator
    {
        $nextPage = Cache::get(self::IMPORT_NEXT_PAGE_KEY . $this->resource);
     
        if ($this->resource === 'practice') {
            return $this->service->fetchAll($nextPage);
        }

        if ($this->resource === 'practice-date-range') {
            return $this->service->fetchByDateRange($this->lastImport, now(), $nextPage);
        }

        throw new Exception('invalid resource ' . $this->resource);
    }

    private function geocode(Practice $practice): void
    {
        if (empty($practice->address)) {
            return;
        }

        if ($practice->wasRecentlyCreated) {
            $geocode = $this->geocodingService->geocodeAddress($practice->address);

            if (isset($geocode)) {
                $practice->lat = $geocode->latitude;
                $practice->lng = $geocode->longitude;
                $practice->save();
            }

            return;
        }

        if ($practice->wasChanged('address')) {
            $geocode = $this->geocodingService->geocodeAddress($practice->address);

            if (isset($geocode)) {
                $practice->lat = $geocode->latitude;
                $practice->lng = $geocode->longitude;
                $practice->save();
            }
        }
    }
}