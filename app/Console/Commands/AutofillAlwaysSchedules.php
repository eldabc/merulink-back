<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Department;
use App\Models\SchedulePlanning;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ScheduleAutofillService;

class AutofillAlwaysSchedules extends Command
{
    // Nombre con el que ejecutarás el comando manualmente si es necesario
    protected $signature = 'schedule-plannings:autofill-schedule';
    protected $description = 'Genera automáticamente la próxima quincena para departamentos configurados.';

    public function handle()
    {
        $this->info('Iniciando proceso de autocompletado automático quincenal...');
        Log::channel('autofill_schedule_plannings')->info("AutofillAlwaysPlanning: Iniciando ejecución cron.");

        // Calcular rango de la PROXIMA quincena
        $today = Carbon::today();
        $start = null;
        $end = null;

        if ($today->day === 1) {
            $start = $today->copy()->startOfMonth();
            $end = $today->copy()->day(15);         
        } elseif ($today->day === 16) {
            $start = $today->copy()->day(16);       
            $end = $today->copy()->endOfMonth();    
        } else {
            // Evitar errores
            $this->error('Este comando solo debe ejecutarse los días 1 o 16 del mes.');
            return Command::FAILURE;
        }

        $startString = $start->toDateString();
        $endString = $end->toDateString();
        $monthNumber = $start->month;

        // Traer departamentos con automatización activa
        $departments = Department::where('autofill_fortnight_always', true)->get();

        foreach ($departments as $department) {
            DB::beginTransaction();
            try {
                // Evaluar los turnos activos en este departamento que tengan available === 'yes'
                $activeAvailableShifts = $department->shifts()->where('available', 'yes')->get();

                $shiftsCount = $activeAvailableShifts->count();

                // Si tiene 0 o más de 1 turno activo 'yes', se apaga la automatización
                if ($shiftsCount !== 1) {
                    $department->update(['autofill_fortnight_always' => false]);
                    
                    $reason = $shiftsCount === 0 
                        ? 'No tiene turnos activos con available="yes"' 
                        : "Tiene múltiples turnos ({$shiftsCount}) con available=\"yes\"";

                    Log::channel('autofill_schedule_plannings')->info("AutofillAlwaysPlanning: Se desactivó la automatización para el departamento ID {$department->id}. Motivo: {$reason}.");
                    $this->warn("Departamento {$department->id} desactivado de la automatización: {$reason}.");
                    
                    DB::commit();
                    continue; // Saltar al siguiente departamento
                }

                // Obtener el único turno disponible
                $activeShift = $activeAvailableShifts->first();

                // Validar si ya existe la planificación para este departamento y periodo
                $planning = SchedulePlanning::where('department_id', $department->id)
                    ->where('start', $startString)
                    ->where('end', $endString)
                    ->first();

                $planningId = $planning ? $planning->id : null;

                // Ejecutar auto-relleno quincenal
                $shiftArray = $activeShift->toArray();

                app(ScheduleAutofillService::class)->execute(
                    $department->id,
                    $start,
                    $end,
                    $shiftArray,
                    $planningId
                );

                DB::commit();
                Log::channel('autofill_schedule_plannings')
                        ->info("Quincena ({$start->format('d-m-Y')} al {$end->format('d-m-Y')}) creada con éxito para el departamento ID {$department->id} usando el turno ID {$activeShift->id}.");

            } catch (\Exception $e) {
                DB::rollBack();
                Log::channel('autofill_schedule_plannings')->info("AutofillAlwaysPlanning: Error procesando departamento ID {$department->id}: " . $e->getMessage());
                $this->error("Error en departamento ID {$department->id}: " . $e->getMessage());
            }
        }

        $this->info('Proceso finalizado.');
        return Command::SUCCESS;
    }
}