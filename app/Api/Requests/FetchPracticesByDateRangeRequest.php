<?php

namespace App\Api\Requests;

use Illuminate\Support\Carbon;

class FetchPracticesByDateRangeRequest implements FetchPracticesRequestContract
{
    private array $query;
    private const string DATE_FORMAT = 'Y-m-d H:i';

    public function __construct(
        private readonly string $token,
        private readonly string $url,
        private readonly Carbon $startDate,
        private readonly Carbon $endDate,
        private readonly bool $excludeUpdates = false,
    ) {
        $queryString = (string) (parse_url($this->url, PHP_URL_QUERY) ?? '');
        parse_str($queryString, $query);
        $this->query = $query;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function headers(): array
    {
        return [
            'Authorization' => 'Token ' . $this->token,
        ];
    }

    public function query(): array 
    { 
        return [
            'start_date' => $this->startDate->format(self::DATE_FORMAT),
            'end_date' => $this->endDate->format(self::DATE_FORMAT),
            'exclude_updates' => $this->excludeUpdates,
        ] + $this->query;
    }
}