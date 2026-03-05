<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LockerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Locker::create([
            'code' => 'C-01',
            'status' => 'Disponible',
            'locker_category_id' => 1,
        ]);
    }
}
