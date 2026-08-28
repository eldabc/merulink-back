<?php

/*
|--------------------------------------------------------------------------
| Definición del menú de la aplicación
|--------------------------------------------------------------------------
|
| Este archivo es LA ÚNICA fuente de verdad de la estructura del menú.
| El frontend NO filtra: el endpoint GET /me/menu devuelve solo los items
| visibles para el usuario, calculados con MenuService a partir de aquí.
|
| Estructura de cada item:
|   - id          (string)  Identificador único (se usa para resaltar item activo)
|   - label       (string)  Texto visible en el menú
|   - path        (string)  Ruta a la que navega (usa "#" si es solo contenedor)
|   - permission  (string)  Permiso requerido (omitir si no exige permiso)
|   - hidden      (bool)    true = módulo no desarrollado, nunca se muestra
|   - hideFromTop (bool)    true = se mantiene en estructura/sidebar pero NO sale en el top menú
|   - children    (array)   Sub-items del sidebar
|
*/

return [

    // ─────────────────────────────────────────────────────────────
    // Módulos desarrollados
    // ─────────────────────────────────────────────────────────────

    [
        'id'     => 'IA',
        'label'  => 'IA',
        'path'   => '/ia',
        'hidden' => true,
    ],

    [
        'id'         => 'RRHH',
        'label'      => 'RRHH',
        'path'       => '/empleados',
        'permission' => 'view-employees',
        'children'   => [
            ['id' => 'Empleados', 'label' => 'Empleados', 'path' => '/empleados', 'permission' => 'view-employees'],
            ['id' => 'Departamentos', 'label' => 'Departamentos', 'path' => '/empleados/departamentos', 'permission' => 'view-departments'],
            ['id' => 'Sub-Departamentos', 'label' => 'Sub-Departamentos', 'path' => '/empleados/sub-departamentos', 'permission' => 'view-subdepartments'],
            ['id' => 'Cargos', 'label' => 'Cargos', 'path' => '/empleados/cargos', 'permission' => 'view-positions'],
            [
                'id'       => 'Roles',
                'label'    => 'Roles',
                'path'     => '#',
                'children' => [
                    ['id' => 'Asignaciones', 'label' => 'Asignaciones', 'path' => '/empleados/roles/asignaciones', 'permission' => 'view-assigns'],
                    ['id' => 'Roles y Permisos', 'label' => 'Roles y Permisos', 'path' => '/empleados/roles', 'permission' => 'view-roles'],
                ],
            ],
            [
                'id'       => 'Planificación',
                'label'    => 'Planificación',
                'path'     => '#',
                'children' => [
                    ['id' => 'Turnos', 'label' => 'Turnos', 'path' => '/empleados/turnos', 'permission' => 'view-shifts'],
                    ['id' => 'Horarios', 'label' => 'Horarios', 'path' => '/empleados/horarios', 'permission' => 'view-schedules'],
                ],
            ],
            [
                'id'       => 'Vestuarios',
                'label'    => 'Vestuarios',
                'path'     => '#',
                'children' => [
                    ['id' => 'Lockers', 'label' => 'Lockers', 'path' => '/empleados/vestuarios/lockers', 'permission' => 'view-lockers'],
                    ['id' => 'Candados', 'label' => 'Candados', 'path' => '/empleados/vestuarios/candados', 'permission' => 'view-padlocks'],
                    ['id' => 'Patrones Candados', 'label' => 'Patrones', 'path' => '/empleados/vestuarios/candados/patrones', 'permission' => 'view-padlocks'],
                    ['id' => 'Casilleros', 'label' => 'Asignaciones', 'path' => '/empleados/vestuarios/casilleros', 'permission' => 'view-assigns'],
                ],
            ],
        ],
    ],

    [
        'id'         => 'Eventos',
        'label'      => 'Eventos',
        // Aterrizaje del módulo (no hay ruta índice en /eventos)
        'path'       => '/eventos/eventos-meru',
        'permission' => 'view-events',
        // No se muestra en el top menú.
        'hideFromTop' => true,
        'children'   => [
            ['id' => 'Events', 'label' => 'Eventos Merú', 'path' => '/eventos/eventos-meru'],
            ['id' => 'EventWeddingAndDinnerHeights', 'label' => 'Noches Bodas / Cena en Alturas', 'path' => '/eventos/noche-bodas-cena-alturas'],
            ['id' => 'VeHolidaysGoogleCalendar', 'label' => 'Festivos VE / Calendario Google', 'path' => '/eventos/festivos-venezolanos-calendario-google'],
            ['id' => 'MeruBirthDays', 'label' => 'Cumpleaños Merú', 'path' => '/eventos/cumpleanos-meru'],
            ['id' => 'ExecutiveMod', 'label' => 'Ejecutivos MOD', 'path' => '/eventos/ejecutivo-mod'],
            ['id' => 'BankingMondays', 'label' => 'Lunes Bancarios', 'path' => '/eventos/lunes-bancarios'],
        ],
    ],

    // ─────────────────────────────────────────────────────────────
    // Módulos futuros (hidden: true = no desarrollados, nunca se muestran)
    // ─────────────────────────────────────────────────────────────

    [
        'id'       => 'Sistemas',
        'label'    => 'Sistemas',
        'path'     => '/sistemas',
        'hidden'   => true,
        'children' => [
            ['id' => 'APs Internet', 'label' => 'APs Internet', 'path' => '/sistemas/aps-internet'],
            ['id' => 'Domotica', 'label' => 'Domotica', 'path' => '/sistemas/domotica'],
            ['id' => 'Mantenimiento', 'label' => 'Mantenimiento', 'path' => '/sistemas/mantenimiento'],
        ],
    ],

    [
        'id'       => 'Inventario',
        'label'    => 'Inventario',
        'path'     => '/inventario',
        'hidden'   => true,
        'children' => [
            ['id' => 'Stock', 'label' => 'Stock', 'path' => '/inventario/stock'],
            ['id' => 'Entradas', 'label' => 'Entradas', 'path' => '/inventario/entradas'],
            ['id' => 'Salidas', 'label' => 'Salidas', 'path' => '/inventario/salidas'],
        ],
    ],

    [
        'id'       => 'Recepcion',
        'label'    => 'Whatsapp',
        'path'     => '/whatsapp',
        'hidden'   => true,
        'children' => [
            ['id' => 'Ventas', 'label' => 'Ventas', 'path' => '/whatsapp/ventas'],
            ['id' => 'AyB', 'label' => 'AyB', 'path' => '/whatsapp/ayb'],
        ],
    ],

    [
        'id'       => 'Ventas-Top',
        'label'    => 'Ventas',
        'path'     => '/ventas',
        'hidden'   => true,
        'children' => [
            ['id' => 'Productos', 'label' => 'Productos', 'path' => '/ventas/productos'],
        ],
    ],

    [
        'id'       => 'Alimentos y Bebidas',
        'label'    => 'Alimentos y Bebidas',
        'path'     => '/ayb',
        'hidden'   => true,
        'children' => [
            ['id' => 'Menu', 'label' => 'Menú', 'path' => '/ayb/menu'],
        ],
    ],

    [
        'id'     => 'Mantenimiento-Top',
        'label'  => 'Mantenimiento',
        'path'   => '/mantenimiento',
        'hidden' => true,
    ],

    [
        'id'       => 'Configuración',
        'label'    => 'Configuración',
        'path'     => '/configuracion',
        'hidden'   => true,
        'children' => [
            ['id' => 'Sistema', 'label' => 'Sistema', 'path' => '/configuracion/sistema'],
            ['id' => 'Seguridad', 'label' => 'Seguridad', 'path' => '/configuracion/seguridad'],
            ['id' => 'Notificaciones', 'label' => 'Notificaciones', 'path' => '/configuracion/notificaciones'],
        ],
    ],

    [
        'id'       => 'Documentos',
        'label'    => 'Documentos',
        'path'     => '/documentos',
        'hidden'   => true,
        'children' => [
            ['id' => 'Memos', 'label' => 'Memos', 'path' => '/documentos/memos'],
            ['id' => 'Reglamento', 'label' => 'Reglamento', 'path' => '/documentos/reglamento'],
        ],
    ],
];
