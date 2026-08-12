<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScheduledDeactivateEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:scheduled-deactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desactivar empleados con desactivación programada.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::channel('scheduled_deactivate_employees')
            ->info('Tarea para desactivar empleados con desactivación programada Iniciada');

            


        Log::channel('scheduled_deactivate_employees')
            ->info('Tarea para desactivar empleados con desactivación programada Finalizada');
    }
}
