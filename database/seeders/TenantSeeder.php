<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::query()->firstOrCreate(['id' => 1]);
        $tenant->inventorySyncEndpoints()->firstOrCreate(
            ['type' => 'inventory_sync'], 
            [
                'root' => config('myevexias.domain'),
                'target' => 'wordpress',
            ],
        );
    }
}
