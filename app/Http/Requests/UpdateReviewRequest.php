<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="UpdateReviewRequest",
 *   @OA\Property(property="auth_type", type="string"),
 *   @OA\Property(property="auth_id", type="integer"),
 *   @OA\Property(property="reviewable_type", type="string"),
 *   @OA\Property(property="reviewable_id", type="integer"),
 *   @OA\Property(property="rating", type="integer", minimum=1, maximum=5),
 *   @OA\Property(property="comment", type="string")
 * )
 */
class UpdateReviewRequest extends FormRequest
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
            'auth_type' => ['sometimes','string','max:255'],
            'auth_id' => ['sometimes','integer','min:1'],
            'reviewable_type' => ['sometimes','string','max:255'],
            'reviewable_id' => ['sometimes','integer','min:1'],
            'rating' => ['sometimes','integer','min:1','max:5'],
            'comment' => ['nullable','string'],
        ];
    }
}
