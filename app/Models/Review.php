<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Review",
 *     type="object",
 *     title="Review",
 *     @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *     @OA\Property(property="auth_id", type="integer", example="1"),
 *     @OA\Property(property="auth_type", type="string", example="App\\Models\\Service"),
 *     @OA\Property(property="reviewable_type", type="string", example="App\\Models\\Service"),
 *     @OA\Property(property="reviewable_id", type="integer", example="1"),
 *     @OA\Property(property="rating", type="integer", example="5"),
 *     @OA\Property(property="comment", type="string", example="This is a great product!"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true"),
 * )
 */
class Review extends Model
{   
    // use SoftDeletes;

    protected $fillable = [
        'auth_type',
        'auth_id',
        'reviewable_id',
        'reviewable_type',
        'rating',
        'comment',
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo($this->auth_type, $this->auth_id);
    }
}
