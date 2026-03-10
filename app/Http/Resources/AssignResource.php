<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignResource extends JsonResource
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
            'assignCode' => $this->assign_code,
            'assignDate' => $this->assign_date,
            'locker' => [
                'id' => $this->locker->id,
                'code' => $this->locker->code,
                'status' => $this->locker->status,
                'category' => [
                    'id' => $this->locker->lockerCategory->id,
                    'key' => $this->locker->lockerCategory->key,
                    'name' => $this->locker->lockerCategory->name,
                ],
                'padlock' => [
                    'id' => $this->padlock->id,
                    'serial' => $this->padlock->serial,
                    'pass' => $this->padlock->pass,
                    'status' => $this->padlock->status,
                ],
            ],
            'employee' => $this->employee_id ?
                    [
                        'id' => $this->employee->id,
                        'name' => $this->employee->first_name.' '.$this->employee->last_name,
                        'sex' => $this->employee->sex,
                        'department' => null,
                        'department_name' => '',
                    ] : null,

        ];
    }
}
