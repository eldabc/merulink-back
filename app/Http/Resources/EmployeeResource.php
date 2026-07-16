<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\PermissionHelper;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $hasVacation = $this->relationLoaded('vacations') && $this->vacations->isNotEmpty();

        $permissionTable = $this->user && $this->user->getAllPermissions()->isNotEmpty()
            ? PermissionHelper::buildTable(
                $this->user->getAllPermissions()->pluck('name')->toArray()
              )
            : null;

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
            'userName' => $this->user?->username,
            'userPass' => null,
            'changePassNextLogin' => $this->user?->change_pass_next_login ?? false,
            'roles' => $this->user ? $this->user->getRoleNames() : [],
            'roleId' => $this->user?->roles->first()?->id,
            // 'permissions' => $this->user ? $this->user->getAllPermissions()->pluck('name') : [],
            'permissionModules' => $permissionTable['modules'] ?? [],
            'permissionSpecials' => $permissionTable['specials'] ?? [],
            'status' => $this->status,
            'useMeruLink' => $this->use_meru_link,
            'useHidCard' => $this->use_hid_card,
            'useLocker' => $this->use_locker,
            'useTransport' => $this->use_transport,
            'contacts' => EmergencyContactResource::collection($this->emergencyContacts),
            'assign' => new AssignResource($this->whenLoaded('assignment')),
            $this->mergeWhen($hasVacation, [
                'vacation' => new VacationResource($this->vacations->first())
            ]),
        ];
    }
}
