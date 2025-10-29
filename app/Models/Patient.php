<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;


/**
 * @OA\Schema(
 *     schema="Patient",
 *     type="object",
 *     title="Patient",
 *     @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *     @OA\Property(property="firstname", type="string", example="John"),
 *     @OA\Property(property="lastname", type="string", example="Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="phone", type="string", example="1234567890"),
 *     @OA\Property(property="address", type="string", example="123 Main St"),
 *     @OA\Property(property="occupation", type="string", example="Doctor"),
 *     @OA\Property(property="image", type="string", example="https://example.com/image.jpg"),
 *     @OA\Property(property="gender", type="string", example="Male"),
 *     @OA\Property(property="city", type="string", example="New York"),
 *     @OA\Property(property="state", type="string", example="New York"),
 *     @OA\Property(property="country", type="string", example="USA"),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="blood_group", type="string", example="A+"),
 *     @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
 *     @OA\Property(property="age", type="integer", example="30"),
 *     @OA\Property(property="height", type="integer", example="180"),
 *     @OA\Property(property="weight", type="integer", example="70"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true"),
 * )
 */
class Patient extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'phone',
        'address',
        'occupation',
        'image',
        'gender',
        'city',
        'state',
        'country',
        'status',
        'blood_group',
        'date_of_birth',
        'age',
        'height',
        'weight',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
