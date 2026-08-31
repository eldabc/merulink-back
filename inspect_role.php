<?php

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (['e.bello', 'admin'] as $username) {
    $user = App\Models\User::where('username', $username)->first();
    if (!$user) {
        echo "== '$username': USUARIO NO ENCONTRADO ==\n\n";
        continue;
    }
    $emp = $user->employee;
    $snap = $emp?->roleSnapshot;
    echo "== '$username' ==\n";
    echo 'USER_ID: ' . $user->id . PHP_EOL;
    echo 'SPATIE_ROLES: ' . json_encode($user->getRoleNames()->toArray()) . PHP_EOL;
    echo 'EMP_ID: ' . ($emp?->id ?? 'null') . PHP_EOL;
    echo 'SNAP_ROLE_ID: ' . ($snap?->role_id ?? 'null') . PHP_EOL;
    echo 'SNAP_ROLE_NAME: ' . ($snap?->role_name ?? 'null') . PHP_EOL;
    echo PHP_EOL;
}

// Muestra los roles disponibles y sus ids
echo "== ROLES EN BD ==\n";
Spatie\Permission\Models\Role::orderBy('id')->get()->each(function ($r) {
    echo 'id=' . $r->id . ' name=' . $r->name . ' label=' . $r->name_label . PHP_EOL;
});
