<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="UpdateWalletRequest",
 *   @OA\Property(property="walletable_type", type="string"),
 *   @OA\Property(property="walletable_id", type="integer"),
 *   @OA\Property(property="balance", type="integer", minimum=0),
 *   @OA\Property(property="currency", type="string")
 * )
 */
class UpdateWalletRequest extends FormRequest
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
            'walletable_type' => ['sometimes','string','max:255'],
            'walletable_id' => ['sometimes','integer','min:1'],
            'balance' => ['sometimes','integer','min:0'],
            'currency' => ['sometimes','string','max:10'],
        ];
    }
}
