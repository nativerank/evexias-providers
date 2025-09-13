<?php

namespace App\Data;

use App\PractitionerType;

final readonly class PractitionerDatum
{
    private function __construct(
        public int $id,
        public string $firstName,
        public string $lastName,
        public string $email,
        public PractitionerType $type,
        public bool $active,
    ) {}

    public static function parse(array $datum): self
    {
        return new self(
            $datum['user_id'],
            $datum['first_name'],
            $datum['last_name'],
            $datum['email'],
            PractitionerType::from($datum['user_type']),
            $datum['is_active'],
        );
    }
}