<?php

namespace App\Data;

use App\Data\Enums\ThirdPartyClass;
use App\Data\Enums\ThirdPartyProvider;

final readonly class ThirdPartyConnectionDatum
{
    public function __construct(
        public ThirdPartyProvider $provider,
        public ?ThirdPartyClass $class,
        public string $externalId,
    ) {}
}