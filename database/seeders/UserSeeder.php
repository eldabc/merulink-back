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
        $superAdmin = User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'password' => Hash::make('7654321'),
            ]
        );

        $superAdmin->assignRole('super-admin');

        $user = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('1234567'),
            ]
        );

        $user->assignRole('admin');

        $admin = User::firstOrCreate(
            ['username' => 'supervisor'],
            [
                'password' => Hash::make('1234567'),
                'change_pass_next_login' => true,
            ]
        );

        $admin->assignRole('supervisor');

        $user = User::firstOrCreate(
            ['username' => 'employee'],
            [
                'password' => Hash::make('1234567'),
            ]
        );

        $user->assignRole('employee');


        $user = User::firstOrCreate(
            ['username' => 'guest'],
            [
                'password' => Hash::make('1234567'),
            ]
        );

        $user->assignRole('guest');

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
    }
}
