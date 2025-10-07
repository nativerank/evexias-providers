<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Practice;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $practices = Practice::all();

        if ($practices->isEmpty()) {
            $this->command->warn('No practices found. Creating some practices first...');
            $practices = Practice::factory(5)->create();
        }

        $practices->each(function ($practice) {
            Lead::factory(rand(5, 15))->create(['practice_id' => $practice->id]);
            Lead::factory(rand(2, 5))->contacted()->create(['practice_id' => $practice->id]);
            Lead::factory(rand(1, 3))->qualified()->create(['practice_id' => $practice->id]);
            Lead::factory(rand(0, 2))->converted()->create(['practice_id' => $practice->id]);
        });

        $this->command->info('Leads seeded successfully!');
    }
}
