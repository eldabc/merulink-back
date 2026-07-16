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
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super-admin',
            'name_label' => 'Super Admin',
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'name_label' => 'Admin',
        ]);

        $supervisorRole = Role::firstOrCreate([
            'name' => 'supervisor',
            'name_label' => 'Supervisor',
        ]);

        $employeeRole = Role::firstOrCreate([
            'name' => 'employee',
            'name_label' => 'Empleado',
        ]);

        $guestRole = Role::firstOrCreate([
            'name' => 'guest',
            'name_label' => 'Invitado',
        ]);

        Permission::firstOrcreate([ 'name' => 'view-calendar' ]);
        Permission::firstOrcreate([ 'name' => 'create-schedules' ]);
        Permission::firstOrcreate([ 'name' => 'view-schedules' ]);
        Permission::firstOrcreate([ 'name' => 'edit-schedules' ]);
        Permission::firstOrcreate([ 'name' => 'delete-schedules' ]);
        Permission::firstOrcreate([ 'name' => 'reviewed-schedules' ]);
        Permission::firstOrcreate([ 'name' => 'approve-schedules' ]);
        Permission::firstOrcreate([ 'name' => 'autofill-schedules' ]);
        
        Permission::firstOrcreate([ 'name' => 'create-employees' ]);
        Permission::firstOrcreate([ 'name' => 'view-employees' ]);
        Permission::firstOrcreate([ 'name' => 'edit-employees' ]);
        Permission::firstOrcreate([ 'name' => 'change-status-employees' ]);


        $superAdminRole->givePermissionTo(Permission::all());
        $adminRole->givePermissionTo(Permission::all());
        $supervisorRole->givePermissionTo([
            'create-schedules', 
            'view-schedules', 
            'edit-schedules', 
            'autofill-schedules',
            'reviewed-schedules',
            'view-employees', 
        ]);
        $employeeRole->givePermissionTo(['view-schedules', 'view-calendar']);
        $guestRole->givePermissionTo(['view-calendar']);
    }
}
