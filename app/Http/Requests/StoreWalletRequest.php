<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="StoreWalletRequest",
 *   required={"walletable_type","walletable_id","currency"},
 *   @OA\Property(property="walletable_type", type="string"),
 *   @OA\Property(property="walletable_id", type="integer"),
 *   @OA\Property(property="balance", type="integer", minimum=0),
 *   @OA\Property(property="currency", type="string")
 * )
 */
class StoreWalletRequest extends FormRequest
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
            'walletable_type' => ['required','string','max:255'],
            'walletable_id' => ['required','integer','min:1'],
            'balance' => ['nullable','integer','min:0'],
            'currency' => ['required','string','max:10'],
        ];
    }
}
