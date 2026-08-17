<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Vacation extends Model
{

    protected $fillable = [
        'start',
        'end',
        'type',
        'observations',
        'employee_id',
    ];

    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope para filtrar vacaciones que se crucen con un periodo dado.
     */
    public function scopeOverlapPeriod(Builder $query, string $start, string $end): Builder
    {
        return $query->where(function ($vQuery) use ($start, $end) {
            $vQuery->whereBetween('start', [$start, $end])
                ->orWhereBetween('end', [$start, $end])
                ->orWhere(function ($deep) use ($start, $end) {
                    $deep->where('start', '<=', $start)
                         ->where('end', '>=', $end);
                });
        });
    }

    /**
     * Scope para filtrar solo registros de tipo 'vacation'.
     */
    public function scopeOnlyVacation(Builder $query): Builder
    {
        return $query->where('type', 'vacation');
    }
}
