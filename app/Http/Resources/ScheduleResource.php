<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
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
            'date' => $this->date,
            'employee' => $this->employee_id,
            'shift_id' => $this->shift_id,

            'letter_shift' => $this->letter_shift ?? null,
            'color' => $this->color ?? null,
            // 'snapshot_code' => $this->snapshot_code,
            // 'snapshot_type' => $this->snapshot_type,
            
            'code' => $this->code,
            'nightShift' => $this->night_shift,
            'typeShift' => $this->type_shift,
            'activePeriodTime' => $this->active_period_time,
            'activePeriodUnitTime' => $this->active_period_unit_time,
            'restPeriodTime' => $this->rest_period_time,
            'restPeriodUnitTime' => $this->rest_period_unit_time,
            'totalPeriodTime' => $this->total_period_time,
            'totalPeriodUnitTime' => $this->total_period_unit_time,
            'checkInTime' => $this->check_in_time,
            'checkOutTime' => $this->check_out_time,
            'allowExit' => $this->allow_exit,
            'allowReScanned' => $this->allow_re_scanned,

        ];
    }
}
