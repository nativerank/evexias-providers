<?php

namespace App\Data;

use App\Data\Enums\ThirdPartyClass;
use App\Data\Enums\ThirdPartyProvider;
use Illuminate\Support\Str;

final readonly class PracticeDatum
{
    private function __construct(
        public int     $id,
        public ?string $uuid,
        public string  $uniqueName,
        public string  $name,
        public string  $phone,
        public string  $marketingEmail,
        public string  $address1,
        public string  $address2,
        public string  $city,
        public string  $state,
        public string  $postalCode,
        public bool    $active,
        public bool    $visible,
        public bool    $elite,
        /** @var ThirdPartyConnectionDatum[] */
        public array   $thirdPartyConnections,
        /** @var PractitionerDatum[] */
        public array   $practitioners,
    )
    {
    }

    public static function parse(array $datum): self
    {
        $thirdPartyConnections = [];

        if (isset($datum['chartedhealth_identifier'])) {
            $thirdPartyConnections[] = new ThirdPartyConnectionDatum(
                ThirdPartyProvider::ChartedHealth,
                ThirdPartyClass::ElectronicHealthRecords,
                $datum['chartedhealth_identifier'],
            );
        }

        $phone = $datum['twilio_number'] ?? null;

        if (! empty($phone) && str_starts_with($phone, '1')) {
            $phone = Str::start($phone, '+');
        }

        return new self(
            $datum['id'],
            $datum['efko_guid'] ?? null,
            $datum['practice_id'],
            preg_replace('/^\[ELITE]\s?/', '', $datum['marketing_name'] ?: $datum['practice_name']),
            $phone,
            $datum['marketing_email'] ?? $datum['pds']['email'],
            $datum['office_address_line_1'],
            $datum['office_address_line_2'],
            $datum['office_city'],
            $datum['office_state'],
            $datum['office_postal_code'],
            $datum['is_active'],
            $datum['visible_on_evexipel'],
            $datum['elite'],
            $thirdPartyConnections,
            array_map([PractitionerDatum::class, 'parse'], $datum['physicians']),
        );
    }

    public function address(): string
    {
        $addressLines = implode(' ', array_filter([
            $this->address1,
            $this->address2,
        ]));
        $cityState = $this->city . ', ' . $this->state;

        return implode(' ', [$addressLines, $cityState, $this->postalCode]);
    }
}
