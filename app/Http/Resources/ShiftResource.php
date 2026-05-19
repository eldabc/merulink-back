<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description,
            'nightShift' => $this->night_shift,
            'typeShift' => $this->type_shift,
            'timeActivePeriod' => $this->time_active_period,
            'durationUnitActivePeriod' => $this->duration_unit_active_period,
            'timeRestPeriod' => $this->time_rest_period,
            'durationUnitRestPeriod' => $this->duration_unit_rest_period,
            'timeTotalPeriod' => $this->time_total_period,
            'durationUnitTotalPeriod' => $this->duration_unit_total_period,
            'checkInTime' => $this->check_in_time,
            'checkOutTime' => $this->check_out_time,
            'allowExit' => $this->allow_exit,
            'allowReScanned' => $this->allow_re_scanned,
            'available' => $this->available,
            'observation' => $this->observation,
            'department' =>  new DepartmentResource($this->whenLoaded('department')),
            'hasSchedule' => $this->schedules()->exists()
        ];
    }
}
