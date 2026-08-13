<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Enums\SystemShift;

class EmployeeFilterScheduleResource extends JsonResource
{
    protected $events;
    protected $isClosed;
    protected $liveShifts;
    protected $holidaysMap;
    protected $isRegistred;

    public function __construct($resource, $events = null, $isClosed = true, $liveShifts = null, $holidaysMap = null, $isRegistred)
    {
        parent::__construct($resource);
        $this->events = $events ?? collect();
        $this->isClosed = $isClosed;
        $this->liveShifts = $liveShifts ?? collect();
        $this->isRegistred = $isRegistred ?? null;
        $this->holidaysMap = $holidaysMap ?? [];
    }

    public function toArray(Request $request): array
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $hasVacation = $this->relationLoaded('vacations') && $this->vacations->isNotEmpty();
        $hasPeriods = $this->relationLoaded('employeePeriods') && $this->employeePeriods->isNotEmpty();
        $globalEvents = $this->events;
      
        $datesMap = [];


        if (!empty($start) && !empty($end)) {
            
            // Indexa los schedules cargados por fecha
            $indexedSchedules = $this->relationLoaded('schedules') ? $this->schedules->keyBy('date') : collect();
            
            // Trae las vacaciones del empleado
            $vacation = $hasVacation ? $this->vacations->first() : null;
            $vacationStart = $vacation ? Carbon::parse($vacation->start)->startOfDay() : null;
            $vacationEnd = $vacation ? Carbon::parse($vacation->end)->startOfDay() : null;

            // Fecha de retiro si el empleado la tiene
            $retireDate = $this->retire_date ? Carbon::parse($this->retire_date)->startOfDay() : null;

            // Crea el periodo de fechas quincenales
            $period = CarbonPeriod::create($start, $end);

            foreach ($period as $date) {
                $hasActiveContractThisDay = false;
                $dateString = $date->format('Y-m-d');
                $currentDate = $date->startOfDay();
                $holidayEvents = [];
                $shiftData = null;
                
                // PRIORIDAD 1: Retiro
                if ($hasPeriods) {
                    $hasActiveContractThisDay = $this->employeePeriods->contains(function ($period) use ($dateString) {
                        // Fecha efectiva de baja: retire_date o scheduled_deactivate_date
                        $deactivationDate = $period->retire_date ?? $period->scheduled_deactivate_date;

                        return $dateString >= $period->hire_date
                            && (is_null($deactivationDate) || $dateString <= $deactivationDate);
                    });
                }

                // SI el empleado registra periodos pero NINGUNO cubre esta fecha, se marca como RETIRO (Baja)
                if ($hasPeriods && !$hasActiveContractThisDay) {
                    $shiftData = SystemShift::RETIREMENT->getData();
                }
                // CASO 2: Turno registrado
                elseif ($indexedSchedules->has($dateString)) {
                    $schedule = $indexedSchedules->get($dateString);
                    
                    // Asigna los datos que tiene schedules (se mantienen actualizados gracias a la actualización masiva en update ShiftsController)
                    $shiftData = [
                        'id' => $schedule->shift_id,
                        'code' => $schedule->code,
                        'letterShift' => $schedule->letter_shift,
                        'color' => $schedule->color,
                        'nightShift' => $schedule->night_shift,
                        'typeShift' => $schedule->type_shift,
                        'checkInTime' => $schedule->check_in_time,
                        'checkOutTime' => $schedule->check_out_time,
                    ];
                }
                // CASO 3: Vacaciones
                elseif ($vacation && $currentDate->between($vacationStart, $vacationEnd)) {
                    $shiftData = SystemShift::VACATIONS->getData();
                } 
                // CASO 4: Día Libre
                else { 
                    $shiftData = SystemShift::FREE->getData();
                }

                // Filtrar eventos con coloring_day
                $dayEvents = collect($globalEvents)->filter(function ($event) use ($dateString) {
                    $eventStartDay = Carbon::parse($event['start'])->format('Y-m-d');
                    $eventEndDay = Carbon::parse($event['end'])->format('Y-m-d');
                    return $dateString >= $eventStartDay && $dateString <= $eventEndDay;
                })->map(function ($event) { return ['title' => $event['title']]; })->values()->all();

                // Cumpleaños
                $currentDateMonthDay = $date->format('m-d');
                $currentMonthDay = $this->birthdate ? Carbon::parse($this->birthdate)->format('m-d') : null;
            
                // Si MM-DD de cumpleaños está dentro de la quincena
                if ($currentMonthDay && $currentMonthDay === $currentDateMonthDay) {
                    $dayBirthdays = [[ 'title' => trim("Cumpleaños {$this->first_name} {$this->last_name}")]];
                } else {
                    $dayBirthdays = [];
                }

                // FERIADOS (Fijos o de Google) busca directamente la fecha
                if (isset($this->holidaysMap[$dateString])) {
                    $holidayEvents[] = [
                        'title' => $this->holidaysMap[$dateString]['title'], 
                        'nonWorking' => true
                    ];
                }
                
                $allDayEvents = array_merge($dayEvents, $dayBirthdays, $holidayEvents);

                $datesMap[$dateString] = [
                    'shift' => $shiftData,
                    'events' => $allDayEvents,
                ];
            }
        }

        return [
            'id' => $this->id,
            'ci' => $this->ci,
            'numEmployee' => $this->num_employee,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'birthdate' => $this->birthdate,
            'department' => [
                'id' => $this->position->department->id,
                'departmentName' => $this->position->department->name,
            ],
            'subDepartment' => $this->position->subDepartment ? [
                'id' => $this->position->subDepartment->id,
                'name' => $this->position->subDepartment->name,
            ] : [],
            'position' => [
                'id' => $this->position->id,
                'name' => $this->position->name
            ],
            'status' => $this->status,
            'retireDate' => $this->retire_date,
            $this->mergeWhen($hasVacation, [
                'vacation' => new VacationResource($this->vacations->first())
            ]),
            'dates' => $datesMap,
        ];
    }
}