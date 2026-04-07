<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventCategory::firstOrCreate(
            ['key' => 'meru-events'],
            [
             'label' => "Eventos Merú",
             'color' => 'meru-events',
            ]
        );

        EventCategory::firstOrCreate(
            ['key' => 'wedding-nights'],
            [
             'label' => "Plan Noche de Bodas",
             'color' => 'wedding-nights',
            ]
        );

        EventCategory::firstOrCreate(
            ['key' => 'dinner-heights'],
            [
             'label' => "Cena en las Alturas",
             'color' => 'dinner-heights',
            ]
        );

        EventCategory::firstOrCreate(
            ['key' => 've-holidays'],
            [
             'label' => "Festivos Venezolanos",
             'color' => 've-holidays',
            ]
        );

        EventCategory::firstOrCreate(
            ['key' => 'google-calendar'],
            [
             'label' => "Calendario Google",
             'color' => 'google-calendar',
            ]
        );

        EventCategory::firstOrCreate(
            ['key' => 'meru-birthdays'],
            [
             'label' => "Cumpleaños Merú",
             'color' => 'meru-birthdays',
            ]
        );

        EventCategory::firstOrCreate(
            ['key' => 'executive-mod'],
            [
             'label' => "Ejecutivos MOD",
             'color' => 'executive-mod',
            ]
        );

         EventCategory::firstOrCreate(
            ['key' => 'banking-mondays'],
            [
             'label' => "Lunes Bancarios",
             'color' => 'banking-mondays',
            ]
        );
    }
}
