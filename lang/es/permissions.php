<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Etiquetas de permisos
    |--------------------------------------------------------------------------
    |
    | Aquí se definen los nombres legibles (labels) de cada módulo, acción
    | y permiso especial. El helper PermissionHelper resuelve automáticamente
    | un permiso como "create-schedules" a "Crear Horarios".
    |
    | Para añadir un nuevo módulo, solo agrega una entrada en 'modules'.
    | Si el permiso no sigue el patrón CRUD (create/view/edit/delete),
    | agrégalo en 'specials'.
    |
    */

    'modules' => [
        'assigns'           => 'Asignaciones',
        'calendar'          => 'Calendario',
        'padlocks'          => 'Candados',
        'positions'         => 'Cargos',
        // 'event-categories'  => 'Categorías de eventos',
        'departments'       => 'Departamentos',
        'employees'         => 'Empleados',
        'events'            => 'Eventos',
        'schedules'         => 'Horarios',
        'lockers'           => 'Lockers',
        // 'schedule-plannings'=> 'Planificaciones',
        // 'event-templates'   => 'Plantillas de eventos',
        'roles'             => 'Roles',
        'subdepartments'    => 'Subdepartamentos',
        'shifts'            => 'Turnos',
        // 'locations'         => 'Ubicaciones',
    ],

    'actions' => [
        'create' => 'Crear',
        'view'   => 'Ver',
        'edit'   => 'Editar',
        'delete' => 'Eliminar',
    ],

    // Permisos especiales
    'specials' => [      

        // Horarios
        'reviewed-schedules'      => 'Revisar horarios',
        'approve-schedules'       => 'Aprobar horarios',
        'autofill-schedules'      => 'Autocompletar horarios',

        // Empleados
        'change-status-employees' => 'Cambiar estado de empleados',
        'manage-merulink-tab-employees'     => 'Gestionar pestaña Meru Link',
    ],
];
