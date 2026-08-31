<?php

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// ============================================================
// Para añadir un permiso cambiar $permName (y si
// desea, los roles). Luego ejecutar este archivo.
// ============================================================
$permName = 'manage-absences-tab-employees';
$guard    = 'sanctum';
$roles    = ['admin']; // roles que reciben el permiso y cuyo snapshot se actualiza

// PASO 1: garantizar el permiso en la tabla `permissions`
$perm = Spatie\Permission\Models\Permission::firstOrCreate(
    ['name' => $permName, 'guard_name' => $guard],
    ['name' => $permName, 'guard_name' => $guard]
);
echo '1) Permiso garantizado, id=' . $perm->id . PHP_EOL;

// PASO 2: asociarlo a los roles configurados (role_has_permissions)
// Importante para que usuarios NUEVOS con esos roles hereden el permiso.
foreach ($roles as $roleName) {
    $role = Spatie\Permission\Models\Role::where('name', $roleName)->where('guard_name', $guard)->first();
    if (!$role) {
        echo "2) Rol '$roleName' NO ENCONTRADO" . PHP_EOL;
        continue;
    }
    if (!$role->hasPermissionTo($permName)) {
        $role->givePermissionTo($perm);
        echo "2) Permiso asignado al rol '$roleName' (id {$role->id})" . PHP_EOL;
    } else {
        echo "2) Rol '$roleName' ya tenía el permiso" . PHP_EOL;
    }
}

// PASO 3: actualizar los role_snapshots de TODOS los empleados cuyo rol
// esté entre los configurados (para que los usuarios existentes lo vean ya).
$roleIds    = Spatie\Permission\Models\Role::whereIn('name', $roles)->where('guard_name', $guard)->pluck('id');
$snapshots  = App\Models\RoleSnapshot::whereIn('role_id', $roleIds)->get();
$updated    = 0;

foreach ($snapshots as $snap) {
    $perms = $snap->permissions ?? [];
    if (!in_array($permName, $perms)) {
        $perms[] = $permName;
        $snap->permissions = $perms;
        $snap->save();
        $updated++;
        echo '3) Snapshot emp ' . $snap->employee_id . ' actualizado (total ' . count($perms) . ')' . PHP_EOL;
    } else {
        echo '3) Snapshot emp ' . $snap->employee_id . ' ya tenía el permiso' . PHP_EOL;
    }
}

if ($updated === 0) {
    echo '3) Ningún snapshot requería cambio (o no hay snapshots para los roles configurados)' . PHP_EOL;
}

echo 'FIN' . PHP_EOL;

