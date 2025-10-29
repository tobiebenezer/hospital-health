<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'doctor_id',
        'day_of_week', // 0-6 (Sunday-Saturday)
        'start_time',  // Format: '09:00'
        'end_time',    // Format: '17:00'
        'is_available'
    ];
    
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
