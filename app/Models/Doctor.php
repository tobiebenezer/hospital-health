<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Doctor",
 *     type="object",
 *     title="Doctor",
 *     @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *     @OA\Property(property="firstname", type="string", example="John"),
 *     @OA\Property(property="lastname", type="string", example="Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="phone", type="string", example="1234567890"),
 *     @OA\Property(property="address", type="string", example="123 Main St"),
 *     @OA\Property(property="specialization", type="string", example="Cardiology"),
 *     @OA\Property(property="experience", type="string", example="10 years"),
 *     @OA\Property(property="education", type="string", example="MD, PhD"),
 *     @OA\Property(property="image", type="string", example="https://example.com/image.jpg"),
 *     @OA\Property(property="gender", type="string", example="Male"),
 *     @OA\Property(property="city", type="string", example="New York"),
 *     @OA\Property(property="state", type="string", example="New York"),
 *     @OA\Property(property="country", type="string", example="USA"),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="bio", type="string", example="This is a great doctor!"),
 *     @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
 *     @OA\Property(property="department", type="string", example="Cardiology"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true"),
 * )
 */
class Doctor extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'phone',
        'address',
        'specialization',
        'experience',
        'education',
        'image',
        'gender',
        'city',
        'state',
        'country',
        'status',
        'bio',
        'date_of_birth',
        'department',
    ];


     protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function labReports()
    {
        return $this->hasMany(LabReport::class);
    }
    
}
