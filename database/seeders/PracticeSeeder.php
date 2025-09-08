<?php

namespace Database\Seeders;

use App\Models\MarketingEmail;
use App\Models\Practice;
use App\Models\Practitioner;
use App\Models\SalesRep;
use Illuminate\Database\Seeder;

class PracticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws \Exception
     */
    public function run(): void
    {
        $reps = SalesRep::query()->get();

        if ($reps->count() === 0) {
            throw new \Exception('No sales reps found. Please run SalesRepSeeder first.');
        }

        Practice::factory()
            ->has(Practitioner::factory()->count(3))
            ->has(MarketingEmail::factory()->count(2))
            ->hasAttached($reps)
            ->count(10)
            ->create();
    }
}
