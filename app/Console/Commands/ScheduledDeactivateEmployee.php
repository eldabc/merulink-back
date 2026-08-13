<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\EmployeeManageService;
use Carbon\Carbon;
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
     * @var EmployeeManageService
     */
    protected $employeeManageService;

    public function __construct(EmployeeManageService $employeeManageService)
    {
        parent::__construct();

        $this->employeeManageService = $employeeManageService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $log = Log::channel('scheduled_deactivate_employees');

        $log->info('Tarea para desactivar empleados con desactivación programada Iniciada');

        // Se desactivan empleados cuya fecha programada ya venció (estrictamente menor a hoy)
        $today = Carbon::now()->startOfDay()->toDateString();

        $employees = Employee::query()
            ->where('status', true)
            ->whereHas('employeePeriods', function ($q) use ($today) {
                $q->whereNotNull('scheduled_deactivate_date')
                    ->whereDate('scheduled_deactivate_date', '<', $today)
                    ->whereNull('retire_date');
            })
            ->with(['employeePeriods', 'user'])
            ->get();

        $deactivated = 0;

        foreach ($employees as $employee) {
            // Periodo vigente con baja programada vencida
            $period = $employee->employeePeriods
                ->whereNotNull('scheduled_deactivate_date')
                ->whereNull('retire_date')
                ->sortByDesc('scheduled_deactivate_date')
                ->first();

            if (!$period) {
                continue;
            }

            try {
                $this->employeeManageService->deactivate($employee, [
                    'effective_date' => $period->scheduled_deactivate_date,
                    'retire_reason'  => $period->retire_reason,
                    'notes'          => $period->notes,
                ]);

                $deactivated++;
                $log->info('Empleado ID ' . $employee->id . ' (' . $employee->first_name . ' ' . $employee->last_name . ') desactivado (baja programada: ' . $period->scheduled_deactivate_date . ').');
            } catch (\Throwable $e) {
                $log->error('Error desactivando al empleado ID ' . $employee->id . ': ' . $e->getMessage());
            }
        }

        $log->info('Tarea finalizada. Empleados desactivados: ' . $deactivated . '.');

        return Command::SUCCESS;
    }
}
