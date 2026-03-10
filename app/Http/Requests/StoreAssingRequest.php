<?php

namespace App\Http\Requests;
use App\Models\Assign;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $existingAssign = Assign::where('locker_id', $this->input('locker.id'))
                                ->where('padlock_id', $this->input('locker.padlock.id'))
                                ->first();
        return [
            'assignCode' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('assigns', 'assign_code')->ignore($existingAssign)
            ],
            'assignDate' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
            ],
            // Accede al ID dentro del objeto locker
            'locker.id' => [
                'required', 'integer', 'exists:lockers,id',
                function ($attribute, $value, $fail) use ($existingAssign) {
                    // Solo falla si el locker está en una asignación que NO sea la actual
                    $alreadyAssigned = Assign::where('locker_id', $value)
                        ->when($existingAssign, function ($query) use ($existingAssign) {
                            return $query->where('id', '!=', $existingAssign->id);
                        })
                        ->exists();

                    if ($alreadyAssigned) {
                        $fail('Este locker ya está asignado en otro registro.');
                    }
                },
            ],
            // Accede al ID dentro del objeto padlock
            'locker.padlock.id' => [
            'required', 'integer', 'exists:padlocks,id',
                function ($attribute, $value, $fail) use ($existingAssign) {
                    // Solo falla si el candado está en una asignación que NO sea la actual
                    $alreadyInUse = Assign::where('padlock_id', $value)
                        ->when($existingAssign, function ($query) use ($existingAssign) {
                            return $query->where('id', '!=', $existingAssign->id);
                        })
                        ->exists();

                    if ($alreadyInUse) {
                        $fail('Este candado ya está siendo usado por otro locker.');
                    }
                },
            ],
            // Accede al ID dentro del objeto employee
            'employee.id' => [
                'nullable', 'integer', 'exists:employees,id',
                function ($attribute, $value, $fail) use ($existingAssign) {
                    if ($value) {
                        // Validamos que el empleado no tenga OTRA asignación diferente a esta
                        $employeeHasAnother = Assign::where('employee_id', $value)
                            ->when($existingAssign, function ($query) use ($existingAssign) {
                                return $query->where('id', '!=', $existingAssign->id);
                            })->exists();

                        if ($employeeHasAnother) {
                            $fail('Este empleado ya tiene una asignación activa.');
                        }
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'assignCode.unique'      => 'Este código de asignación ya está registrado.',
            'locker.id.exists'       => 'El locker seleccionado no es válido.',
            'locker.padlock.id.exists'  => 'El candado seleccionado no es válido.',
            'locker.id.required'     => 'El locker es obligatorio.',
            'locker.padlock.id.required' => 'El candado es obligatorio.',
            'employee.id.exists'     => 'El empleado seleccionado no existe.',
            'employee.id.integer'    => 'El id de empleado debe ser un número entero',
            'assignDate.date'        => 'La fecha de asignación no tiene un formato válido.',
        ];
    }
}
