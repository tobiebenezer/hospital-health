<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'exists:doctors,id'],
            'patient_id' => ['required', 'exists:patients,id'],
            'start_time' => [
                'required',
                'date',
                'after:now',
                Rule::unique('appointments')->where(function ($query) {
                    return $query->where('doctor_id', $this->doctor_id);
                })
            ],
            'end_time' => ['required', 'date', 'after:start_time'],
            'notes' => ['nullable', 'string', 'max:500']
        ];
    }
}
