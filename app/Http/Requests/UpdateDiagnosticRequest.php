<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="UpdateDiagnosticRequest",
 *   @OA\Property(property="prescription", type="string"),
 *   @OA\Property(property="report_id", type="integer"),
 *   @OA\Property(property="diagnostics", type="string"),
 *   @OA\Property(property="patient_id", type="integer"),
 *   @OA\Property(property="doctor_id", type="integer"),
 *   @OA\Property(property="status", type="string")
 * )
 */
class UpdateDiagnosticRequest extends FormRequest
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
            'prescription' => ['nullable','string'],
            'report_id' => ['sometimes','nullable','integer','min:1','exists:lab_reports,id'],
            'diagnostics' => ['nullable','string'],
            'patient_id' => ['sometimes','nullable','integer','min:1','exists:patients,id'],
            'doctor_id' => ['sometimes','nullable','integer','min:1','exists:doctors,id'],
            'status' => ['nullable','string','max:255'],
        ];
    }
}
