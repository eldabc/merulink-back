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
        $guard = 'sanctum';

        // ROLES
        $adminRole = Role::firstOrCreate([
            'name'       => 'admin',
            'guard_name' => $guard,
        ], [
            'name_label' => 'Admin',
        ]);

        $supervisorRole = Role::firstOrCreate([
            'name'       => 'supervisor',
            'guard_name' => $guard,
        ], [
            'name_label' => 'Supervisor',
        ]);

        $employeeRole = Role::firstOrCreate([
            'name'       => 'employee',
            'guard_name' => $guard,
        ], [
            'name_label' => 'Empleado',
        ]);

        $guestRole = Role::firstOrCreate([
            'name'       => 'guest',
            'guard_name' => $guard,
        ], [
            'name_label' => 'Invitado',
        ]);

        // PERMISOS
        $permissions = [
            // Horarios
            'create-schedules', 'view-schedules', 'edit-schedules', 'delete-schedules',
            'reviewed-schedules', 'approve-schedules', 'autofill-schedules',
            // Empleados
            'create-employees', 'view-employees', 'edit-employees',
            'change-status-employees', 'manage-merulink-tab-employees', 'manage-absences-tab-employees',
            // Calendario
            'view-calendar',
            // Lockers
            'create-lockers', 'view-lockers', 'edit-lockers', 'delete-lockers',
            // Candados
            'create-padlocks', 'view-padlocks', 'edit-padlocks', 'delete-padlocks',
            // Roles
            'create-roles', 'view-roles', 'edit-roles', 'delete-roles',
            // Asignaciones
            'create-assigns', 'view-assigns', 'edit-assigns', 'delete-assigns',
            // Departamentos
            'create-departments', 'view-departments', 'edit-departments', 'delete-departments',
            // Subdepartamentos
            'create-subdepartments', 'view-subdepartments', 'edit-subdepartments', 'delete-subdepartments',
            // Cargos
            'create-positions', 'view-positions', 'edit-positions', 'delete-positions',
            // Eventos
            'create-events', 'view-events', 'edit-events', 'delete-events',
            // Turnos
            'create-shifts', 'view-shifts', 'edit-shifts', 'delete-shifts',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name'       => $permissionName,
                'guard_name' => $guard,
            ]);
        }

        // ASIGNACIÓN DE PERMISOS
        // Busca los permisos del guard correcto, para evitar problemas de guard mismatch al asignar permisos
        $allPermissions = Permission::where('guard_name', $guard)->get();
        
        $adminRole->syncPermissions($allPermissions);
        
        $supervisorRole->syncPermissions([
            'create-schedules', 
            'view-schedules', 
            'edit-schedules', 
            'autofill-schedules',
            'reviewed-schedules',
            'view-employees', 
        ]);

        $employeeRole->syncPermissions(['view-schedules', 'view-calendar']);
        $guestRole->syncPermissions(['view-calendar']);
    }
}