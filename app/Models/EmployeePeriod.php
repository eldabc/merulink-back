<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePeriod extends Model
{
    protected $fillable = [
        'hire_date',
        'retire_date',
        'retire_reason',
        'notes',
        'employee_id',
    ];

    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope para filtrar periodos vigentes en un rango de fechas.
     */
    public function scopeActiveInPeriod(Builder $query, string $start, string $end): Builder
    {
        return $query->where(function ($sub) use ($start, $end) {
            // El empleado sigue activo (sin fecha de retiro)
            $sub->whereNull('retire_date')
                ->where('hire_date', '<=', $end);
        })->orWhere(function ($sub) use ($start, $end) {
            // El empleado se retiró, pero su contrato cubre al menos un día del periodo consultado
            $sub->whereNotNull('retire_date')
                ->where('hire_date', '<=', $end)
                ->where('retire_date', '>=', $start);
        });
    }
}
