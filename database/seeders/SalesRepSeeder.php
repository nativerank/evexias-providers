<?php

namespace Database\Seeders;

use Database\Factories\SalesRepFactory;
use Illuminate\Database\Seeder;

class SalesRepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SalesRepFactory::new()
            ->count(2)
            ->create();
    }
}
