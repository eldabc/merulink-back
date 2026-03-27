<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Assign;
use Illuminate\Http\Request;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\StoreEmployeeRequest;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::query();

        // Filtro por sexo (H o M)
        if ($request->has('sex')) {
            $query->where('sex', $request->sex);
        }

        // Filtro empleados SIN asignación activa
        if ($request->boolean('unassigned')) {
            $query->whereDoesntHave('assignment'); 
        }

        $employees = $query->with([
            'position.department', 
            'position.subDepartment',
            'assignment'
        ])->get();

        return EmployeeResource::collection($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();
        return DB::transaction(function () use ($data) {
            // return response()->json([ 'data' => $data, 'dataDos' => $data['assign_id'] ]);

            // Crear
            $employeeData = collect($data)->except(['assign_id', 'contacts'])->toArray();
            $employee = Employee::create($data);

            if (isset($data['assign_id'])) {
                $assignment = Assign::find($data['assign_id']);

                if ($assignment) {
                    $assignment->update([
                        'assign_code' => 'ASG' . $assignment->locker->code . '-' . now()->format('d-m-Y'),
                        'assign_date' => now()->format('Y-m-d'),
                        'employee_id' => $employee->id,
                    ]);
                }
            }

            if (isset($data['contacts'])) {
                foreach ($data['contacts'] as $contact) {
                    $employee->emergencyContacts()->create($contact);
                }
            }

            return new EmployeeResource($employee->load([
                'position.department', 
                'position.subDepartment',
                'assignment'
            ]));
        });

    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }
}
