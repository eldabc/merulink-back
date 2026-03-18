<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    public function employee(): hasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function subDepartments(): hasMany
    {
        return $this->hasMany(SubDepartment::class);
    }
}
