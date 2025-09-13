<?php

namespace App\Importers;

use App\Api\Exceptions\MyEvexiasRequestFailedException;
use App\Data\PracticeDatum;
use App\Repositories\PracticeRepository;
use App\Services\PracticeService;
use Generator;
use Illuminate\Support\Facades\Cache;

class PracticeImporter
{
    private const string LAST_IMPORT_KEY = 'myevexias-com-import:last-timestamp:practice-date-range';
    private const string IMPORT_NEXT_PAGE_KEY = 'myevexias-com-import:next-page:';

    public function __construct(
        private readonly PracticeService $service,
        private readonly PracticeRepository $repository,
        private readonly PractitionerImporter $practitionerImporter,
    ) {}

    public function import(): void
    {
        try {
            $this->runImport();
        } catch (MyEvexiasRequestFailedException $exception) {
            if (in_array($exception->resource, ['practice', 'practice-date-range'])) {
                Cache::put(self::IMPORT_NEXT_PAGE_KEY . $exception->resource, $exception->nextLink, now()->addMinutes(30));
            }

            throw $exception;
        }
    }

    private function runImport(): void
    {
        $start = now();

        $generator = $this->generator();

        /** @var PracticeDatum $practiceDatum */
        foreach($generator as $practiceDatum) {
            if (! $practiceDatum->active) {
                // $this->repository->delete($practiceDatum);
                continue;
            }

            if (! $practiceDatum->visible) {
                // $this->repository->delete($practiceDatum);
                continue;
            }

            logger()->debug('importing practice', ['practice' => $practiceDatum->id]);

            $practice = $this->repository->save($practiceDatum);
            $this->practitionerImporter->import($practice, $practiceDatum->practitioners);
        }

        Cache::put(self::LAST_IMPORT_KEY, $start);
    }

    private function generator(): Generator
    {
        $lastImport = Cache::get(self::LAST_IMPORT_KEY);
     
        if (is_null($lastImport)) {
            $nextPage = Cache::get(self::IMPORT_NEXT_PAGE_KEY . 'practice');
            return $this->service->fetchAll($nextPage);
        }

        $nextPage = Cache::get(self::IMPORT_NEXT_PAGE_KEY . 'practice-date-range');
        return $this->service->fetchByDateRange($lastImport, now(), $nextPage);
    }
}