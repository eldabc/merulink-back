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
            'description' => $this->description,
            'active_period' => $this->active_period,
            'rest_period' => $this->rest_period,
            'total_period' => $this->total_period,
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
            'allow_exit' => $this->allow_exit,
            'allow_re_scanned' => $this->allow_re_scanned,
            'available' => $this->available,
            'night_shift' => $this->night_shift,
            'observations' => $this->observations,
            'department' =>  new DepartmentResource($this->whenLoaded('department')),
        ];
    }
}
