<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     schema="LabReport",
 *     type="object",
 *     title="LabReport",
 *     @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *     @OA\Property(property="patient_id", type="integer", example="1"),
 *     @OA\Property(property="doctor_id", type="integer", example="1"),
 *     @OA\Property(property="report", type="string", example="This is a great product!"),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="tests", type="string", example="This is a great product!"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true"),
 * )
 */
class LabReport extends Model
{

    protected $fillable = [
        'patient_id',
        'report',
        'status',
        'tests',
        'doctor_id'
    ];
}
