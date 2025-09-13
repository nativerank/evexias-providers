<?php

namespace App\Repositories;

use App\Data\PractitionerDatum;
use App\Models\Practice;
use App\Models\Practitioner;

class PractitionerRepository
{
    public function save(Practice $practice, PractitionerDatum $practitionerDatum): Practitioner
    {
        $practitioner = Practitioner::query()->updateOrCreate(
            ['external_id' => $practitionerDatum->id],
            [
                'first_name' => $practitionerDatum->firstName,
                'last_name' => $practitionerDatum->lastName,
                'email' => $practitionerDatum->email,
                'practitioner_type' => $practitionerDatum->type,
                'active' => $practitionerDatum->active,
            ],
        );

        $practitioner->practices()->syncWithoutDetaching($practice);
        
        return $practitioner;
    }
}