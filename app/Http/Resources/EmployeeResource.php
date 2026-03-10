<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'numEmployee' => $this->num_employee,
            'firstName' => $this->first_name,
            'secondName' => $this->second_name,
            'lastName' => $this->last_name,
            'secondLastName' => $this->second_last_name,
            'birthDate' => $this->birthDate,
            'placeOfBirth' => $this->place_of_birth,
            'nationality' => $this->nationality,
            'sex' => $this->sex,
            'maritalStatus' => $this->marital_status,
            'department' => $this->department_id,
        ];
    }
}
