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

    public function delete(PracticeDatum $practiceDatum): ?bool
    {
        $practice = Practice::query()->firstWhere(['external_id' => $practiceDatum->id]);

        if (is_null($practice)) {
            return null;
        }

        return $practice->delete();
    }

    public function cleanUp(array $existing)
    {
        Practice::query()->whereNotIn('id', $existing)->lazy()->each->delete();
    }
}