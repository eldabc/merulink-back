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

        // Horarios
        Permission::firstOrcreate([ 'name' => 'create-schedules' ]);
        Permission::firstOrcreate([ 'name' => 'view-schedules' ]);
        Permission::firstOrcreate([ 'name' => 'edit-schedules' ]);
        Permission::firstOrcreate([ 'name' => 'delete-schedules' ]);
        Permission::firstOrcreate([ 'name' => 'reviewed-schedules' ]);
        Permission::firstOrcreate([ 'name' => 'approve-schedules' ]);
        Permission::firstOrcreate([ 'name' => 'autofill-schedules' ]);
        
        // Empleados
        Permission::firstOrcreate([ 'name' => 'create-employees' ]);
        Permission::firstOrcreate([ 'name' => 'view-employees' ]);
        Permission::firstOrcreate([ 'name' => 'edit-employees' ]);
        Permission::firstOrcreate([ 'name' => 'change-status-employees' ]);
        Permission::firstOrcreate([ 'name' => 'manage-merulink-tab-employees' ]);
        
        // Calendario
        Permission::firstOrcreate([ 'name' => 'view-calendar' ]);

        // Lockers
        Permission::firstOrcreate([ 'name' => 'create-lockers' ]);
        Permission::firstOrcreate([ 'name' => 'view-lockers' ]);
        Permission::firstOrcreate([ 'name' => 'edit-lockers' ]);
        Permission::firstOrcreate([ 'name' => 'delete-lockers' ]);

        // Candados
        Permission::firstOrcreate([ 'name' => 'create-padlocks' ]);
        Permission::firstOrcreate([ 'name' => 'view-padlocks' ]);
        Permission::firstOrcreate([ 'name' => 'edit-padlocks' ]);
        Permission::firstOrcreate([ 'name' => 'delete-padlocks' ]);

        // Roles
        Permission::firstOrcreate([ 'name' => 'create-roles' ]);
        Permission::firstOrcreate([ 'name' => 'view-roles' ]);
        Permission::firstOrcreate([ 'name' => 'edit-roles' ]);
        // Permission::firstOrcreate([ 'name' => 'delete-roles' ]);

        // Asignaciones
        Permission::firstOrcreate([ 'name' => 'create-assigns' ]);
        Permission::firstOrcreate([ 'name' => 'view-assigns' ]);
        Permission::firstOrcreate([ 'name' => 'edit-assigns' ]);
        Permission::firstOrcreate([ 'name' => 'delete-assigns' ]);

        // Departamentos
        Permission::firstOrcreate([ 'name' => 'create-departments' ]);
        Permission::firstOrcreate([ 'name' => 'view-departments' ]);
        Permission::firstOrcreate([ 'name' => 'edit-departments' ]);
        Permission::firstOrcreate([ 'name' => 'delete-departments' ]);

        // Subdepartamentos
        Permission::firstOrcreate([ 'name' => 'create-subdepartments' ]);
        Permission::firstOrcreate([ 'name' => 'view-subdepartments' ]);
        Permission::firstOrcreate([ 'name' => 'edit-subdepartments' ]);
        Permission::firstOrcreate([ 'name' => 'delete-subdepartments' ]);

        // Cargos
        Permission::firstOrcreate([ 'name' => 'create-positions' ]);
        Permission::firstOrcreate([ 'name' => 'view-positions' ]);
        Permission::firstOrcreate([ 'name' => 'edit-positions' ]);
        Permission::firstOrcreate([ 'name' => 'delete-positions' ]);

        // Eventos
        Permission::firstOrcreate([ 'name' => 'create-events' ]);
        Permission::firstOrcreate([ 'name' => 'view-events' ]);
        Permission::firstOrcreate([ 'name' => 'edit-events' ]);
        Permission::firstOrcreate([ 'name' => 'delete-events' ]);

        // Turnos
        Permission::firstOrcreate([ 'name' => 'create-shifts' ]);
        Permission::firstOrcreate([ 'name' => 'view-shifts' ]);
        Permission::firstOrcreate([ 'name' => 'edit-shifts' ]);
        Permission::firstOrcreate([ 'name' => 'delete-shifts' ]);

        // Planificaciones
        // Permission::firstOrcreate([ 'name' => 'create-schedule-plannings' ]);
        // Permission::firstOrcreate([ 'name' => 'view-schedule-plannings' ]);
        // Permission::firstOrcreate([ 'name' => 'edit-schedule-plannings' ]);
        // Permission::firstOrcreate([ 'name' => 'delete-schedule-plannings' ]);


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
