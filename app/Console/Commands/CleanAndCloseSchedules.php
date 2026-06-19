<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SchedulePlanning;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanAndCloseSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule-plannings:close-and-clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cierra quincenas vencidas y limpia registros con más de 2 meses de antiguedad.';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // ----------------------------------------------------
        // Cerrar quincenas pasadas
        // ----------------------------------------------------
        Log::channel('close_clean_and_schedule_plannings')
            ->info("Iniciando cierre de quincenas vencidas...");
        
        $now = Carbon::now();
        
        // Evalua planning_schedules que sigan 'abiertos' 
        $affectedRows = SchedulePlanning::whereNot('status', 'closed')
            ->where('end', '<', $now->toDateString())
            ->update(['status' => 'closed']);

        Log::channel('close_clean_and_schedule_plannings')
            ->info("Se cerraron automáticamente {$affectedRows} planificaciones vencidas.");


        // ----------------------------------------------------
        // Borrar registros con más de 2 meses en el pasado
        // ----------------------------------------------------
        Log::channel('close_clean_and_schedule_plannings')
            ->info("Iniciando limpieza de historial antiguo...");
        
        $twoMonthsAgo = Carbon::now()->subMonths(2)->startOfDay();

        // Borrar los horarios que cumplieron los dos meses
        $deletedSchedules = SchedulePlanning::where('end', '<', $twoMonthsAgo->toDateString())->delete();

        Log::channel('close_clean_and_schedule_plannings')
            ->info("Limpieza completada. Se eliminaron {$deletedSchedules} registros antiguos.");

        return Command::SUCCESS;
    }
}
