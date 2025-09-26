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
            ],
            [
                'efko_guid' => $practiceDatum->uuid,
                'unique_name' => $practiceDatum->uniqueName,
                'name' => $practiceDatum->name,
                'address' => $practiceDatum->address(),
                'phone' => $practiceDatum->phone,
                'status' => $practiceDatum->elite ? PracticeStatus::Elite : null,
                'tenant_id' => Tenant::query()->sole()->id,
            ],
        );

        $this->saveConnections($practice, $practiceDatum);
        $this->saveMarketingEmails($practice, $practiceDatum);

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

    private function saveConnections(Practice $practice, PracticeDatum $practiceDatum): void
    {
        $connections = [];
        foreach ($practiceDatum->thirdPartyConnections as $connection) {
            $connection = $practice->thirdPartyConnections()->updateOrCreate(
                [
                    'provider' => $connection->provider->value,
                    'external_id' => $connection->externalId,
                ],
                [
                    'class' => $connection->class?->value,
                ]
            );

            $connections[] = $connection->id;
        }

        $practice->thirdPartyConnections()->whereNotIn('id', $connections)->delete();
    }

    private function saveMarketingEmails(Practice $practice, PracticeDatum $practiceDatum): void
    {
        $marketingEmail = $practice->marketingEmails()->updateOrCreate(
            [
                'email' => $practiceDatum->marketingEmail,
            ]
        );

        $practice->marketingEmails()->sync([$marketingEmail->id]);
    }
}