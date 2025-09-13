<?php

namespace App\Repositories;

use App\Data\PracticeDatum;
use App\Models\Practice;
use App\Models\Tenant;
use App\PracticeStatus;

class PracticeRepository
{
    public function save(PracticeDatum $practiceDatum): Practice
    {
        $practice = Practice::query()->updateOrCreate(
            [
                'external_id' => $practiceDatum->id,
                'unique_name' => $practiceDatum->uniqueName,
            ],
            [
                'name' => $practiceDatum->name,
                'address' => $practiceDatum->address(),
                'phone' => $practiceDatum->phone,
                'status' => $practiceDatum->elite ? PracticeStatus::Elite : null,
                'tenant_id' => Tenant::query()->sole()->id,
            ],
        );

        return $practice;
    }
}