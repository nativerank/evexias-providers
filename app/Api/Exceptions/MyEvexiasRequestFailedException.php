<?php

namespace App\Api\Exceptions;

use Exception;
use Throwable;

class MyEvexiasRequestFailedException extends Exception
{
    public function __construct(
        public readonly string $resource,
        public readonly string $nextLink,
        Throwable $previous,
    ) {
        parent::__construct(
            'failed fetching evexias api resource: ' . $resource, 
            previous: $previous,
        );
    }

    public function context(): array
    {
        return [
            'resource' => $this->resource,
            'nextLink' => $this->nextLink,
        ];
    }
}