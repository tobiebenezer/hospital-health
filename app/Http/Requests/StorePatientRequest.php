<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="StorePatientRequest",
 *   required={"firstname","lastname","email","password"},
 *   @OA\Property(property="firstname", type="string"),
 *   @OA\Property(property="lastname", type="string"),
 *   @OA\Property(property="email", type="string", format="email"),
 *   @OA\Property(property="password", type="string", format="password", minimum=8),
 *   @OA\Property(property="phone", type="string"),
 *   @OA\Property(property="address", type="string"),
 *   @OA\Property(property="occupation", type="string"),
 *   @OA\Property(property="image", type="string"),
 *   @OA\Property(property="gender", type="string", enum={"Male","Female","Other"}),
 *   @OA\Property(property="city", type="string"),
 *   @OA\Property(property="state", type="string"),
 *   @OA\Property(property="country", type="string"),
 *   @OA\Property(property="status", type="string", enum={"active","inactive"}),
 *   @OA\Property(property="blood_group", type="string", enum={"A+","A-","B+","B-","AB+","AB-","O+","O-"}),
 *   @OA\Property(property="date_of_birth", type="string", format="date"),
 *   @OA\Property(property="age", type="integer", minimum=0),
 *   @OA\Property(property="height", type="integer"),
 *   @OA\Property(property="weight", type="integer")
 * )
 */
class StorePatientRequest extends FormRequest
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
            'firstname' => ['required','string','max:255'],
            'lastname' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:patients,email'],
            'password' => ['required','string','min:8'],
            'phone' => ['nullable','string','max:255'],
            'address' => ['nullable','string'],
            'occupation' => ['nullable','string','max:255'],
            'image' => ['nullable','string','max:2048'],
            'gender' => ['nullable','in:Male,Female,Other'],
            'city' => ['nullable','string','max:255'],
            'state' => ['nullable','string','max:255'],
            'country' => ['nullable','string','max:255'],
            'status' => ['nullable','in:active,inactive'],
            'blood_group' => ['nullable','in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'date_of_birth' => ['nullable','date'],
            'age' => ['nullable','integer','min:0'],
            'height' => ['nullable','integer'],
            'weight' => ['nullable','integer'],
        ];
    }
}
