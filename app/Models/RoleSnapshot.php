<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleSnapshot extends Model
{
    protected $fillable = [
        'role_id',
        'employee_id',
        'role_name',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
