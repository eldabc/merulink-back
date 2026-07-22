<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feriados Fijos de Venezuela
    |--------------------------------------------------------------------------
    | Lista de feriados nacionales que ocurren en la misma fecha cada año.
    | Formato: 'MM-DD' => 'Nombre del feriado'
    |
    | Esta es la fuente única de verdad. HolidayService y GoogleCalendarService
    | leen de aquí.
    */
    'fixed' => [
        '01-01' => 'Año Nuevo',
        '05-01' => 'Día del Trabajador',
        '06-24' => 'Batalla de Carabobo',
        '07-05' => 'Día de la Independencia',
        '07-24' => 'Natalicio de Simón Bolívar',
        '10-12' => 'Día de la Resistencia Indígena',
        '12-24' => 'Víspera de Navidad',
        '12-25' => 'Navidad',
        '12-31' => 'Fin de Año',
    ],

];
