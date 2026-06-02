<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

class EmployeeFilterScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $hasVacation = $this->relationLoaded('vacations') && $this->vacations->isNotEmpty();

        $datesMap = [];

        if (!empty($start) && !empty($end)) {
            
            // Indexa los schedules cargados por fecha
            $indexedSchedules = $this->relationLoaded('schedules') ? $this->schedules->keyBy('date') : collect();
            
            // Trae las vacaciones del empleado
            $vacation = $hasVacation ? $this->vacations->first() : null;
            $vacationStart = $vacation ? \Carbon\Carbon::parse($vacation->start)->startOfDay() : null;
            $vacationEnd = $vacation ? \Carbon\Carbon::parse($vacation->end)->startOfDay() : null;

            // Crea el periodo de fechas quincenales
            $period = \Carbon\CarbonPeriod::create($start, $end);

            foreach ($period as $date) {
                $dateString = $date->format('Y-m-d');

                // CASO 1: Si la fecha esta registrada
                if ($indexedSchedules->has($dateString)) {
                    $schedule = $indexedSchedules->get($dateString);
                    $datesMap[$dateString] = [
                        'shift' => [
                            'id' => $schedule->shift_id,
                            'code' => $schedule->code,
                            'letterShift' => $schedule->letter_shift,
                            'color' => $schedule->color,
                            'nightShift' => $schedule->night_shift,
                            'typeShift' => $schedule->type_shift,
                            'checkInTime' => $schedule->check_in_time,
                            'checkOutTime' => $schedule->check_out_time,
                        ]
                    ];
                } 
                // CASO 2: Rango dentro de sus Vacaciones
                elseif ($vacation && $date->between($vacationStart, $vacationEnd)) {
                    $datesMap[$dateString] = [
                        'shift' => [
                            'id' => -1,
                            'code' => 'VAC',
                            'letterShift' => 'VAC',
                            'color' => '#d0d5d6', 
                            'nightShift' => null,
                            'typeShift' => null,
                            'checkInTime' => null,
                            'checkOutTime' => null,
                        ]
                    ];

                } else { // CASO 3: Optimización de BD - Día Libre automático

                    $datesMap[$dateString] = [
                        'shift' => [
                            'id' => 0,
                            'code' => 'L',
                            'letterShift' => 'L',
                            'color' => '#535759',
                            'nightShift' => null,
                            'typeShift' => null,
                            'checkInTime' => null,
                            'checkOutTime' => null,
                        ]
                    ];
                }
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
            $this->mergeWhen($hasVacation, [
                'vacation' => new VacationResource($this->vacations->first())
            ]),
            'dates' => $datesMap, // Retorna la estructura para tu Ag-Grid
        ];
    }
}