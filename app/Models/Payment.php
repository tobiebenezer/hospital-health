<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Payment",
 *     type="object",
 *     title="Payment",
 *     @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *     @OA\Property(property="paymentable_id", type="integer", example="1"),
 *     @OA\Property(property="paymentable_type", type="string", example="App\\Models\\User"),
 *     @OA\Property(property="payment_method", type="string", example="credit_card"),
 *     @OA\Property(property="amount", type="integer", example="100"),
 *     @OA\Property(property="currency", type="string", example="USD"),
 *     @OA\Property(property="payment_status", type="string", example="pending"),
 *     @OA\Property(property="transaction_id", type="string", example="1234567890"),
 *     @OA\Property(property="payment_date", type="string", format="date-time", example="2022-01-01 00:00:00"),
 *     @OA\Property(property="meta", type="array", @OA\Items(), example="[]"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true"),
 * )
 */
class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'paymentable_id',
        'paymentable_type',
        'payment_method',
        'amount',
        'currency',
        'payment_status',
        'transaction_id',
        'payment_date',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Get the parent paymentable model (order or appointment).
     */
    public function paymentable()
    {
        return $this->morphTo();
    }
}
