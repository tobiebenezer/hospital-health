<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="StoreReviewRequest",
 *   required={"auth_type","auth_id","reviewable_type","reviewable_id","rating"},
 *   @OA\Property(property="auth_type", type="string"),
 *   @OA\Property(property="auth_id", type="integer"),
 *   @OA\Property(property="reviewable_type", type="string"),
 *   @OA\Property(property="reviewable_id", type="integer"),
 *   @OA\Property(property="rating", type="integer", minimum=1, maximum=5),
 *   @OA\Property(property="comment", type="string")
 * )
 */
class StoreReviewRequest extends FormRequest
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
            'auth_type' => ['required','string','max:255'],
            'auth_id' => ['required','integer','min:1'],
            'reviewable_type' => ['required','string','max:255'],
            'reviewable_id' => ['required','integer','min:1'],
            'rating' => ['required','integer','min:1','max:5'],
            'comment' => ['nullable','string'],
        ];
    }
}
