<?php

namespace App\Http\Controllers;

use App\Models\Vacation;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\VacationResource;
use App\Http\Requests\StoreVacationRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VacationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVacationRequest $request)
    {
        $data = $request->validated();

        // Valida que la nueva ausencia no cruce con otra ya registrada
        $overlap = Vacation::where('employee_id', $data['employee_id'])
            ->overlapPeriod($data['start'], $data['end'])
            ->exists();

        if ($overlap) {
            return ApiResponseHelper::createResponse(
                'fail',
                'absence_overlap',
                'El empleado ya tiene una ausenciads registrada en el periodo indicado',
                null,
                409
            );
        }

        // Solo se permite un registro de vacaciones por año.
        if ($data['type'] === 'vacation') {
            $year = Carbon::parse($data['start'])->year;

            $alreadyHasVacation = Vacation::where('employee_id', $data['employee_id'])
                ->where('type', 'vacation')
                ->whereYear('start', $year)
                ->exists();

            if ($alreadyHasVacation) {
                return ApiResponseHelper::createResponse(
                    'fail',
                    'vacation_already_registered',
                    "El empleado ya tiene vacaciones registradas en el año {$year}. Solo se permite un registro de vacaciones por año.",
                    null,
                    409
                );
            }
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
    public function update(Request $request, Vacation $vacation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vacation $vacation)
    {
        //
    }
}
