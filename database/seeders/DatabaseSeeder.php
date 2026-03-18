<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(RoleSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(SubDepartmentSeeder::class);
        $this->call(EmployeeSeeder::class);
        $this->call(PadlockPatternSeeder::class);
        $this->call(PadlockSeeder::class);
        $this->call(LockerCategorySeeder::class);
        $this->call(LockerSeeder::class);
        $this->call(AssignSeeder::class);
        $this->call(UserSeeder::class);      

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
