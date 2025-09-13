<?php

namespace App\Data;

use Throwable;

final readonly class PracticeDatum
{
    private function __construct(
        public int $id,
        public string $uniqueName,
        public string $name,
        public string $phone,
        public string $marketingEmail,
        public string $address1,
        public string $address2,
        public string $city,
        public string $state,
        public string $postalCode,
        public bool $active,
        public bool $visible,
        public bool $elite,
        public array $practitioners,
    ) {}

    public static function parse(array $datum): self
    {
        return new self(
            $datum['id'],
            $datum['practice_id'],
            $datum['marketing_name'] ?: $datum['practice_name'],
            $datum['twilio_number'],
            $datum['marketing_email'] ?? $datum['pds']['email'] ?? $datum['dsl']['email'],
            $datum['office_address_line_1'],
            $datum['office_address_line_2'],
            $datum['office_city'],
            $datum['office_state'],
            $datum['office_postal_code'],
            $datum['is_active'],
            $datum['visible_on_evexipel'],
            $datum['elite'],
            array_map([PractitionerDatum::class, 'parse'], $datum['physicians']),
        );
    }

    public function address(): string 
    {
        $parts = array_filter([
            $this->address1,
            $this->address2,
            $this->city,
            $this->state,
            $this->postalCode,
        ]);

        return implode(' ', $parts);
    }
}