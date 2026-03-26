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
            'ci' => $this->ci,
            'numEmployee' => $this->num_employee,
            'firstName' => $this->first_name,
            'secondName' => $this->second_name,
            'lastName' => $this->last_name,
            'secondLastName' => $this->second_last_name,
            'birthdate' => $this->birthdate,
            'placeOfBirth' => $this->place_of_birth,
            'nationality' => $this->nationality,
            'sex' => $this->sex,
            'maritalStatus' => $this->marital_status,
            'bloodType' => $this->blood_type,
            'email' => $this->email,
            'mobilePhone' => $this->mobile_phone,
            'homePhone' => $this->home_phone,
            'address' => $this->address,
            'joinDate' => $this->join_date,
            // 'department' => new DepartmentResource($this->department),
            'department' => [
                'id' => $this->position->department->id,
                'departmentName' => $this->position->department->name,
            ],
            'subDepartment' => $this->position->subdepartment ? [
                'id' => $this->position->subdepartment->id,
                'name' => $this->position->subdepartment->name,
            ] : [],
            'position' => [
                'id' => $this->position->id,
                'name' => $this->position->name
            ],
            'userName' => $this->user_name,
            'userPass' => $this->user_pass,
            'changePassNextLogin' => $this->change_pass_next_login,
            'status' => $this->status,
            'useMeruLink' => $this->use_meru_link,
            'useHidCard' => $this->use_hid_card,
            'useLocker' => $this->use_locker,
            'useTransport' => $this->use_transport,
            'contacts' => EmergencyContactResource::collection($this->emergencyContacts),
            // 'lockerAssing' => $this->lockerAssingId,
        ];
    }
}
