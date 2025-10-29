<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="UpdateDoctorRequest",
 *   @OA\Property(property="firstname", type="string"),
 *   @OA\Property(property="lastname", type="string"),
 *   @OA\Property(property="email", type="string", format="email"),
 *   @OA\Property(property="password", type="string", format="password", minimum=8),
 *   @OA\Property(property="phone", type="string"),
 *   @OA\Property(property="address", type="string"),
 *   @OA\Property(property="specialization", type="string"),
 *   @OA\Property(property="experience", type="string"),
 *   @OA\Property(property="education", type="string"),
 *   @OA\Property(property="image", type="string"),
 *   @OA\Property(property="gender", type="string", enum={"Male","Female","Other"}),
 *   @OA\Property(property="city", type="string"),
 *   @OA\Property(property="state", type="string"),
 *   @OA\Property(property="country", type="string"),
 *   @OA\Property(property="status", type="string", enum={"active","inactive"}),
 *   @OA\Property(property="bio", type="string"),
 *   @OA\Property(property="date_of_birth", type="string", format="date"),
 *   @OA\Property(property="department", type="string")
 * )
 */
class UpdateDoctorRequest extends FormRequest
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
        $doctorId = $this->route('doctor');
        return [
            'firstname' => ['sometimes','string','max:255'],
            'lastname' => ['sometimes','string','max:255'],
            'email' => ['sometimes','email','max:255', Rule::unique('doctors','email')->ignore($doctorId)],
            'password' => ['sometimes','string','min:8'],
            'phone' => ['nullable','string','max:255'],
            'address' => ['nullable','string'],
            'specialization' => ['nullable','string','max:255'],
            'experience' => ['nullable','string','max:255'],
            'education' => ['nullable','string','max:255'],
            'image' => ['nullable','string','max:2048'],
            'gender' => ['nullable','in:Male,Female,Other'],
            'city' => ['nullable','string','max:255'],
            'state' => ['nullable','string','max:255'],
            'country' => ['nullable','string','max:255'],
            'status' => ['nullable','in:active,inactive'],
            'bio' => ['nullable','string'],
            'date_of_birth' => ['nullable','date'],
            'department' => ['nullable','string','max:255'],
        ];
    }
}
