<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="UpdateSystemSettingRequest",
 *   @OA\Property(property="key", type="string"),
 *   @OA\Property(property="value", type="string")
 * )
 */
class UpdateSettingsRequest extends FormRequest
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
        $id = $this->route('settings');
        return [
            'key' => ['sometimes','string','max:255', Rule::unique('settings','key')->ignore($id)],
            'value' => ['sometimes','string'],
        ];
    }
}
