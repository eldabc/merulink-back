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
        $adminUser = Role::firstOrCreate([
            'name' => 'admin',
        ]);

        $supervisorUser = Role::firstOrCreate([
            'name' => 'supervisor',
        ]);

        $employeeUser = Role::firstOrCreate([
            'name' => 'employee',
        ]);

        $guestUser = Role::firstOrCreate([
            'name' => 'guest',
        ]);

        Permission:: firstOrcreate([ 'name' => 'view-calendar' ]);
        Permission:: firstOrcreate([ 'name' => 'create-schedules' ]);
        Permission:: firstOrcreate([ 'name' => 'view-schedules' ]);
        Permission:: firstOrcreate([ 'name' => 'edit-schedules' ]);
        Permission:: firstOrcreate([ 'name' => 'delete-schedules' ]);
        Permission:: firstOrcreate([ 'name' => 'approve-schedules' ]);
        
        Permission:: firstOrcreate([ 'name' => 'create-employees' ]);
        Permission:: firstOrcreate([ 'name' => 'view-employees' ]);
        Permission:: firstOrcreate([ 'name' => 'edit-employees' ]);
        Permission:: firstOrcreate([ 'name' => 'delete-employees' ]);


        $adminUser->givePermissionTo(Permission::all());
        $supervisorUser->givePermissionTo(['create-schedules', 'view-schedules', 'edit-schedules', 'view-employees']);
        $employeeUser->givePermissionTo(['view-schedules', 'view-calendar']);
        $guestUser->givePermissionTo(['view-schedules', 'view-calendar']);
         


    }
}
