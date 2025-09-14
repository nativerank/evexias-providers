<?php 

namespace App\Importers;

use App\Models\Practice;
use App\Repositories\PractitionerRepository;

class PractitionerImporter
{
    public function __construct(
        private readonly PractitionerRepository $repository,
    ) {}

    public function import(Practice $practice, array $practitionersData): array
    {
        $practitioners = array_map([$this->repository, 'save'], $practitionersData);

        $practice->practitioners()->sync($practitioners);

        return $practitioners;
    }
}