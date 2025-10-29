<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="StoreLabReportRequest",
 *   required={"patient_id"},
 *   @OA\Property(property="patient_id", type="integer"),
 *   @OA\Property(property="doctor_id", type="integer"),
 *   @OA\Property(property="report", type="string"),
 *   @OA\Property(property="status", type="string"),
 *   @OA\Property(property="tests", type="string")
 * )
 */
class StoreLabReportRequest extends FormRequest
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
        return [
            'patient_id' => ['required','integer','min:1'],
            'doctor_id' => ['nullable','integer','min:1'],
            'report' => ['nullable','string'],
            'status' => ['nullable','string','max:255'],
            'tests' => ['nullable','string'],
        ];
    }
}
