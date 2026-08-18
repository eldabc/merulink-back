<?php

namespace App\Http\Controllers;

use App\Models\Vacation;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\VacationResource;
use App\Http\Requests\StoreVacationRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VacationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vacation::query();

        // Filtro opcional por empleado
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->integer('employee_id'));
        }

        $vacations = $query->orderBy('start')->get();

        return ApiResponseHelper::createResponse(
            'ok',
            'vacations_list',
            'Ausencias obtenidas correctamente',
            VacationResource::collection($vacations)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVacationRequest $request)
    {
        $data = $request->validated();

        if ($error = $this->validateAbsenceRules($data['employee_id'], $data['type'], $data['start'], $data['end'])) {
            return $error;
        }

        $vacation = Vacation::create([
            'type'         => $data['type'],
            'start'        => $data['start'],
            'end'          => $data['end'],
            'observations' => $data['observations'],
            'employee_id'  => $data['employee_id'],
        ]);

        return ApiResponseHelper::createResponse(
            'ok',
            'created_vacation',
            'Ausencia registrada correctamente',
            new VacationResource($vacation),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Vacation $vacation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreVacationRequest $request, Vacation $vacation)
    {
        $data = $request->validated();

        // Solo se puede editar si la ausencia aún no ha comenzado (start > hoy)
        if (Carbon::parse($vacation->start)->lte(Carbon::today())) {
            return ApiResponseHelper::createResponse(
                'fail',
                'vacation_not_editable',
                'Esta ausencia ya comenzó y no puede editarse',
                null,
                422
            );
        }

        if ($error = $this->validateAbsenceRules($vacation->employee_id, $data['type'], $data['start'], $data['end'], $vacation->id)) {
            return $error;
        }

        $vacation->update([
            'type'         => $data['type'],
            'start'        => $data['start'],
            'end'          => $data['end'],
            'observations' => $data['observations'],
        ]);

        return ApiResponseHelper::createResponse(
            'ok',
            'updated_vacation',
            'Ausencia actualizada correctamente',
            new VacationResource($vacation->fresh())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vacation $vacation)
    {
        //
    }

    /**
     * Valida las reglas para una ausencia:
     * - No debe cruzarse con otra ausencia del mismo empleado.
     * - Si es vacación, solo un registro por año (según la fecha de inicio).
     *
     * return: error (JsonResponse) si alguna regla falla,
     * o null si la ausencia puede guardarse/actualizarse.
     */
    private function validateAbsenceRules(int $employeeId, string $type, string $start, string $end, ?int $ignoreId = null): ?JsonResponse
    {
        // Choque de periodos (excluye el registro actual si se está editando)
        $overlapQuery = Vacation::where('employee_id', $employeeId)->overlapPeriod($start, $end);
        if ($ignoreId) {
            $overlapQuery->where('id', '!=', $ignoreId);
        }

        if ($overlapQuery->exists()) {
            return ApiResponseHelper::createResponse(
                'fail',
                'absence_overlap',
                'El empleado ya tiene una ausencia registrada en el periodo indicado',
                null,
                409
            );
        }

        // Solo un registro de vacaciones por año
        if ($type === 'vacation') {
            $year = Carbon::parse($start)->year;

            $vacationQuery = Vacation::where('employee_id', $employeeId)
                ->where('type', 'vacation')
                ->whereYear('start', $year);
            if ($ignoreId) {
                $vacationQuery->where('id', '!=', $ignoreId);
            }

            if ($vacationQuery->exists()) {
                return ApiResponseHelper::createResponse(
                    'fail',
                    'vacation_already_registered',
                    "El empleado ya tiene vacaciones registradas en el año {$year}. Solo se permite un registro de vacaciones por año.",
                    null,
                    409
                );
            }
        }

        return null;
    }
}
