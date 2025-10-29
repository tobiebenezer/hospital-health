<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Diagnostic",
 *     type="object",
 *     title="Diagnostic",
 *     @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *     @OA\Property(property="prescription", type="string", example="Prescription"),
 *     @OA\Property(property="report_id", type="integer", example="1"),
 *     @OA\Property(property="diagnostics", type="string", example="Diagnostics"),
 *     @OA\Property(property="patient_id", type="integer", example="1"),
 *     @OA\Property(property="doctor_id", type="integer", example="1"),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true"),
 * )
 */
class Diagnostic extends Model
{
    protected $fillable = [
        'prescription',
        'report_id',
        'diagnostics',
        'patient_id',
        'doctor_id',
        'status',
    ];
}
