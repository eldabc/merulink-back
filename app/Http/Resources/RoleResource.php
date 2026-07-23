<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\PermissionHelper;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $permissions = $this->permissions->pluck('name')->toArray();

        return [
            'value'            => $this->id,
            'label'            => $this->name_label ?? ucfirst($this->name),
            'name'             => $this->name,
            'permissionGroups' => PermissionHelper::buildGroupedPermissions($permissions),
        ];
    }
}
