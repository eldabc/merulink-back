<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'departmentName' => $this->name,
            'subDepartments' => SubDepartmentResource::collection($this->whenLoaded('subDepartments')),
            'positions' => PositionResource::collection($this->whenLoaded('positions')),
        ];
    }
}
