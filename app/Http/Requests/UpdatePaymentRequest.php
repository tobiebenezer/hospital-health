<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="UpdatePaymentRequest",
 *   @OA\Property(property="paymentable_type", type="string"),
 *   @OA\Property(property="paymentable_id", type="integer"),
 *   @OA\Property(property="payment_method", type="string"),
 *   @OA\Property(property="amount", type="integer", minimum=0),
 *   @OA\Property(property="currency", type="string"),
 *   @OA\Property(property="payment_status", type="string", enum={"pending","paid","failed"}),
 *   @OA\Property(property="transaction_id", type="string"),
 *   @OA\Property(property="payment_date", type="string", format="date-time"),
 *   @OA\Property(property="meta", type="array", @OA\Items())
 * )
 */
class UpdatePaymentRequest extends FormRequest
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
            'paymentable_type' => ['sometimes','string','max:255'],
            'paymentable_id' => ['sometimes','integer','min:1'],
            'payment_method' => ['nullable','string','max:255'],
            'amount' => ['sometimes','integer','min:0'],
            'currency' => ['nullable','string','max:10'],
            'payment_status' => ['nullable','in:pending,paid,failed'],
            'transaction_id' => ['nullable','string','max:255'],
            'payment_date' => ['nullable','date'],
            'meta' => ['nullable','array'],
        ];
    }
}
