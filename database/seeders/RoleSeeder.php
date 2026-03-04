<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleSuperAdmin = Role::firstOrCreate([
            'name' => 'super-admin',
        ]);

        $roleAdmin = Role::firstOrCreate([
            'name' => 'admin',
        ]);

        $roleUser = Role::firstOrCreate([
            'name' => 'user',
        ]);

        Permission:: firstOrcreate([
            'name' => 'crear departamento',
        ]);

        Permission:: firstOrcreate([
            'name' => 'ver departamento',
        ]);

        Permission:: firstOrcreate([
            'name' => 'editar departamento',
        ]);

        $roleSuperAdmin->givePermissionTo(Permission::all());
        $roleAdmin->givePermissionTo('ver departamento');


    }
}
