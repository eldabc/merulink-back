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

        Permission:: firstOrcreate([ 'name' => 'create-schedules' ]);
        Permission:: firstOrcreate([ 'name' => 'view-schedules' ]);
        Permission:: firstOrcreate([ 'name' => 'edit-schedules' ]);
        Permission:: firstOrcreate([ 'name' => 'delete-schedules' ]);
        Permission:: firstOrcreate([ 'name' => 'approve-schedules' ]);

        $roleSuperAdmin->givePermissionTo(Permission::all());
        $roleAdmin->givePermissionTo(['create-schedules', 'view-schedules', 'edit-schedules' ]);
        $roleUser->givePermissionTo('view-schedules');
         


    }
}
