<?php

namespace Database\Seeders;

use App\Models\Locker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LockerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Locker::create([
            'code' => 'D-01',
            'status' => 'Ocupado',
            'locker_category_id' => 1,
        ]);

        Locker::create([
            'code' => 'D-02',
            'status' => 'Disponible',
            'locker_category_id' => 1,
        ]);

        Locker::create([
            'code' => 'C-01',
            'status' => 'Disponible',
            'locker_category_id' => 2,
        ]);

         Locker::create([
            'code' => 'C-02',
            'status' => 'Disponible',
            'locker_category_id' => 2,
        ]);
    }
}
