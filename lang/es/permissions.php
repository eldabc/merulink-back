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
        'schedules'         => 'Horarios',
        'employees'         => 'Empleados',
        'calendar'          => 'Calendario',
        'lockers'           => 'Lockers',
        'padlocks'          => 'Candados',
        'assigns'           => 'Asignaciones',
        'departments'       => 'Departamentos',
        'subdepartments'    => 'Subdepartamentos',
        'positions'         => 'Cargos',
        'events'            => 'Eventos',
        'event-categories'  => 'Categorías de eventos',
        'event-templates'   => 'Plantillas de eventos',
        'locations'         => 'Ubicaciones',
        'shifts'            => 'Turnos',
        'schedule-plannings'=> 'Planificaciones',
    ],

    'actions' => [
        'create' => 'Crear',
        'view'   => 'Ver',
        'edit'   => 'Editar',
        'delete' => 'Eliminar',
    ],

    'specials' => [
        // Permisos especiales
        // (view-calendar se resuelve automáticamente como "Ver Calendario")

        // Horarios
        'reviewed-schedules'      => 'Revisar horarios',
        'approve-schedules'       => 'Aprobar horarios',
        'autofill-schedules'      => 'Autocompletar horarios',

        // Empleados
        'change-status-employees' => 'Cambiar estado de empleados',
    ],
];
