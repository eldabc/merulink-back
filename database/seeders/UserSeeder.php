<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Merulink',
                'password' => Hash::make('123456'),
            ]
        );

        $user->assignRole('admin');

        $admin = User::firstOrCreate(
            ['username' => 'supervisor'],
            [
                'name' => 'Supervisor Merulink',
                'password' => Hash::make('123456'),
            ]
        );

        $admin->assignRole('supervisor');

        $user = User::firstOrCreate(
            ['username' => 'employee'],
            [
                'name' => 'Empleado Merulink',
                'password' => Hash::make('123456'),
            ]
        );

        $user->assignRole('employee');


        $user = User::firstOrCreate(
            ['username' => 'guest'],
            [
                'name' => 'Invitado Merulink',
                'password' => Hash::make('123456'),
            ]
        );

        $user->assignRole('guest');

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
    }
}
