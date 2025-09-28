<?php

namespace App\Api\Requests;

final readonly class FetchPracticesRequest implements FetchPracticesRequestContract
{
    private array $query;

    public function __construct(
        private string $token,
        private string $url,
        private ?int   $id = null,
    )
    {
        $queryString = (string)(parse_url($this->url, PHP_URL_QUERY) ?? '');
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
        $query = $this->query;
        if (isset($this->id)) {
            $query['id'] = $this->id;
        }

        return $query;
    }
}
