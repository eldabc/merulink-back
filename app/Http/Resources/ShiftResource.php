<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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

        $alert = null;
        // Carbon 3 devuelve diferencias firmadas por defecto: hay que pedir el valor absoluto
        // (now()->diffInDays($updated_at) devuelve negativo si $updated_at es pasado).
        $daysSinceUpdate = Carbon::now()->diffInDays($this->updated_at, true);
        $isModifiedInDifferentDay = !$this->updated_at->isSameDay($this->created_at);

        if ($isModifiedInDifferentDay && $daysSinceUpdate <= 20) {
            $formattedDate = $this->updated_at->format('d/m/Y');
            $alert = [
                'type'     => 'new_modification',
                'label'    => 'NUEVO',
                'tooltip'  => "Este turno fue modificado el $formattedDate.",
            ];
        }

        return [
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description,
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
            'available' => $this->available,
            'availableFrom' => $this->available_from->format('Y-m-d'),
            'observation' => $this->observation,
            'department' =>  new DepartmentResource($this->whenLoaded('department')),
            'hasSchedule' => $this->schedules()->exists(),
            'letterShift' => $this->letter_shift ?? null,
            'color' => $this->color ?? null,
            'alert' => $alert,
            'createdAt' => $this->created_at->format('Y-m-d')
        ];
    }
}
