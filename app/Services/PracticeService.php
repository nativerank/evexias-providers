<?php

namespace App\Services;

use App\Api\Exceptions\MyEvexiasRequestFailedException;
use App\Api\PracticeApi;
use App\Api\Requests\FetchPracticesByDateRangeRequest;
use App\Api\Requests\FetchPracticesRequest;
use App\Api\Requests\FetchPracticesRequestContract;
use App\Data\PracticeDatum;
use Exception;
use Generator;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;

class PracticeService 
{
    private const string PRACTICES_ENDPOINT = 'https://myevexias.com/api/marketing/practices';
    private const string PRACTICES_DATE_RANGE_ENDPOINT = 'https://myevexias.com/api/marketing/practices-date-range';

    public function __construct(
        private readonly string $key,
        private readonly PracticeApi $api,
    ) {}

    public function fetchAll(?string $next = null): Generator
    {
        $next ??= self::PRACTICES_ENDPOINT;
        
        do {
            $request = new FetchPracticesRequest($this->key, $next);
            $generator = $this->fetch($request);
            yield from $generator;

            $next = $generator->getReturn();
        } while(! empty($next));
    }

    public function fetchByDateRange(Carbon $startDate, Carbon $endDate, ?string $next = null): Generator
    {
        $next ??= self::PRACTICES_DATE_RANGE_ENDPOINT;
        
        do {
            $request = new FetchPracticesByDateRangeRequest($this->key, $next, $startDate, $endDate);
            $generator = $this->fetch($request);
            yield from $generator;

            $next = $generator->getReturn();
        } while(! empty($next));
    }

    public function fetchOne(int $id): ?PracticeDatum
    {
        $request = new FetchPracticesRequest($this->key, self::PRACTICES_ENDPOINT, $id);

        try {
            $response = $this->api->fetch($request);
        } catch(RequestException $exception) {
            throw new MyEvexiasRequestFailedException('practice', self::PRACTICES_ENDPOINT, $exception);
        }

        $results = $response->json('results');

        if (empty($results)) {
            return null;
        }

        return PracticeDatum::parse($results[0]);
    }
   
    private function fetch(FetchPracticesRequestContract $request): Generator
    {
        try {
            $response = $this->api->fetch($request);
        } catch (RequestException $exception) {
            throw new MyEvexiasRequestFailedException('practice', $request->url(), $exception);
        }
        
        $json = $response->json();
        $next = $json['next'];
        $results = $json['results'];

        foreach ($results as $result) {
            yield PracticeDatum::parse($result);
        }

        return $next;
    }
}