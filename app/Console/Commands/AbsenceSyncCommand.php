<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use App\Services\TemporaryDeactivationService;
use Illuminate\Console\Command;

class AbsenceSyncCommand extends Command
{
    protected $signature = 'employees:sync-absences';

    protected $description = 'Suspende y reanuda los servicios de empleados según sus ausencias (vacaciones/reposos).';

    public function handle(TemporaryDeactivationService $service): int
    {
        Log::channel('temporary_deactivation_employees')
            ->info('Iniciando tarea para desactivación/activación temporal de empleados');

        $stats = $service->sync();

        Log::channel('temporary_deactivation_employees')
            ->info(sprintf(
                'Sincronización completada: %d ausencias procesadas (suspendidas: %d, restauradas: %d, limpiezas retroactivas: %d).',
                array_sum($stats),
                $stats['suspended'],
                $stats['restored'],
                $stats['cleaned']
            ));

        $this->info('Servicios de empleados sincronizados según ausencias.');

        return self::SUCCESS;
    }
}
