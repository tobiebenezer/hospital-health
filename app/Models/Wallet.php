<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     schema="Wallet",
 *     type="object",
 *     title="Wallet",
 *     @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *     @OA\Property(property="walletable_id", type="integer", example="1"),
 *     @OA\Property(property="walletable_type", type="string", example="App\\Models\\User"),
 *     @OA\Property(property="balance", type="integer", example="100"),
 *     @OA\Property(property="currency", type="string", example="USD"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true"),
 * )
 */
class Wallet extends Model
{
    protected $fillable = [
        'walletable_id',
        'walletable_type',
        'balance',
        'currency',
    ];

    public function walletable()
    {
        return $this->morphTo();
    }
}
