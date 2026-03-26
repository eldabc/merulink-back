<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
        // $standardText = ['required', 'string'];
        return [
            'ci' => [
                'required',
                'numeric',   
                Rule::unique('employees', 'ci')->ignore($this->route('employee')),
            ],
            'numEmployee' => [
                'required',
                'numeric',   
                Rule::unique('employees', 'num_employee')->ignore($this->route('employee')),
            ],
           'firstName' => [
                'required',
                'string',
            ],
            'secondName' => [
                'string',
            ],
            'lastName' => [
                'required',
                'string',
            ],
            'secondLastName' => [
                'string',
            ],
            'birthdate' => [
                'date',
                'string',
            ],
            'placeOfBirth' => [
                'string',
            ],
            'nationality' => [
                'required',
                'string',
            ],
            'age' => [
                'numeric',
            ],
            'sex' => [
                'required',
                'string',
            ],
            'maritalStatus' => [
                'string',
            ],
            'bloodType' => [
                'string',
            ],
            'email' => [
                'email',
                'required',
                'string',
            ],
            
            'mobilePhone' => [
                'required',
                'numeric',
            ],

            'homePhone' => [
                'numeric',
            ],

            'address' => [
                'string',
            ],

            'joinDate' => [
                'required',
                'date',
            ],

            'department' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            'subDepartment' => [
                'nullable',
                'integer',
                'exists:sub_departments,id',
            ],

            'position' => [
                'required',
                'integer',
                'exists:positions,id',
            ],

            'userName' => [
                'string',
            ],

            'userPass' => [
                'string',
            ],

            'changePassNextLogin' => [
                'boolean',
            ],

            'status' => [
                'required',
                'boolean'
            ],

            'useMeruLink' => [
                'boolean'
            ],

            'useHidCard' => [
                'boolean'
            ],

            'useLocker' => [
                'boolean'
            ],

            'useTransport' => [
                'boolean'
            ],

            'contacts' => [
                'nullable',
                'array',
                'min:1',
                'max:5',
            ],

            'lockerAssingId' => [
                'required',
                'integer',
                'exists:assigns,id',
            ],
        ];
    }
}
