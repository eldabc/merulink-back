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
            ['email' => 'rodrigo@mail.com'],
            [
                'name' => 'Rodrigo',
                'password' => Hash::make('123456'),
            ]
        );

        $user->assignRole('super-admin');

        $admin = User::firstOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'User',
                'password' => Hash::make('123456'),
            ]
        );

        $admin->assignRole('user');
    }
}
