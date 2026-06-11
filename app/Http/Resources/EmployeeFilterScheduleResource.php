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

    public function __construct($resource, $events = null, $isClosed = true, $liveShifts = null)
    {
        parent::__construct($resource);
        $this->events = $events ?? collect();
        $this->isClosed = $isClosed;
        $this->liveShifts = $liveShifts ?? collect(); 
    }
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
                $dateString = $date->format('Y-m-d');
                $currentDate = $date->startOfDay();
                $shiftData = null;

                // PRIORIDAD 1: Retiro
                if ($retireDate && $currentDate->greaterThan($retireDate)) {
                    $shiftData = SystemShift::RETIREMENT->getData();
                }
                // CASO 2: Si la fecha está registrada
                elseif ($indexedSchedules->has($dateString)) {
                    $schedule = $indexedSchedules->get($dateString);
                    
                    // COMPORTAMIENTO LIVE: Si está ABIERTA, busca en la colección
                    if (!$this->isClosed && $this->liveShifts->isNotEmpty()) {
                        
                        // Buscar el turno en schedule
                        $liveShift = $this->liveShifts->first(function($shift) use ($schedule) {
                            return $shift->id == $schedule->shift_id;
                        });

                        if ($liveShift) {
                            $shiftData = [
                                'id'          => $liveShift->id,
                                'code'        => $liveShift->code,
                                'letterShift' => $liveShift->letter_shift, //$liveShift->letterShift ?? 
                                'color'       => $liveShift->color,
                                'nightShift'  => $liveShift->night_shift, //$liveShift->nightShift ?? 
                                'typeShift'   => $liveShift->type_shift, //$liveShift->typeShift ?? 
                                'checkInTime' => $liveShift->check_in_time, //$liveShift->checkInTime ?? 
                                'checkOutTime'=> $liveShift->check_out_time, //$liveShift->checkOutTime ?? 
                            ];
                        }
                    }
                    // Si está CERRADA asigna data de schedule
                    else {
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
                }
                // CASO 3: Vacaciones
                elseif ($vacation && $currentDate->between($vacationStart, $vacationEnd)) {
                    $shiftData = SystemShift::VACATIONS->getData();
                } 
                // CASO 4: Día Libre
                else { 
                    $shiftData = SystemShift::FREE->getData();
                }

                // Busca si hay eventos con coloring_day activo para ESTA fecha
                $dayEvents = collect($globalEvents)->filter(function ($event) use ($dateString) {
                    $eventStartDay = Carbon::parse($event['start'])->format('Y-m-d');
                    $eventEndDay = Carbon::parse($event['end'])->format('Y-m-d');
                    return $dateString >= $eventStartDay && $dateString <= $eventEndDay;
                })->map(function ($event) { return ['title' => $event['title']]; })->values()->all();

                $currentDateMonthDay = $date->format('m-d');
                $currentMonthDay = $this->birthdate ? Carbon::parse($this->birthdate)->format('m-d') : null;
            
                // Si MM-DD de cumpleaños está dentro de la quincena
                if ($currentMonthDay && $currentMonthDay === $currentDateMonthDay) {
                    $dayBirthdays = [[ 'title' => trim("Cumpleaños {$this->first_name} {$this->last_name}")]];
                } else $dayBirthdays = [];
                
                $allDayEvents = array_merge($dayEvents, $dayBirthdays);

                // Estructura final del día
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