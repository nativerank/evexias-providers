<?php 

namespace App\Importers;

use App\Models\Practice;
use App\Repositories\PractitionerRepository;

class PractitionerImporter
{
    public function __construct(
        private readonly PractitionerRepository $repository,
    ) {}

    public function import(Practice $practice, array $practitionersData)
    {
        foreach ($practitionersData as $practitionerDatum) {
            $this->repository->save($practice, $practitionerDatum);
        }
    }
}