<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LockerCategory;

class LockerCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LockerCategory::firstOrCreate(['key' => 'D'], ['name' => 'Damas']);
        LockerCategory::firstOrCreate(['key' => 'C'], ['name' => 'Caballeros']);
    }
}
