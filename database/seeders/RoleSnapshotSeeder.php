<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\RoleSnapshot;
use Spatie\Permission\Models\Role;

class RoleSnapshotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuarios que tienen empleado vinculado (user_id en employees no nulo)
        $users = User::whereHas('employee')->with('employee')->get();

        foreach ($users as $user) {
            // Obtener el primer rol Spatie del usuario
            $spatieRole = $user->roles->first();
            if (!$spatieRole) continue;

            // Permisos que tiene ese rol en Spatie
            $permissions = $spatieRole->permissions->pluck('name')->toArray();

            RoleSnapshot::updateOrCreate(
                ['employee_id' => $user->employee->id],
                [
                    'role_id'     => $spatieRole->id,
                    'role_name'   => $spatieRole->name_label ?? $spatieRole->name,
                    'permissions' => $permissions,
                ]
            );

            $this->command->info("Snapshot creado para {$user->username}: {$spatieRole->name} (" . count($permissions) . " permisos)");
        }
    }
}
