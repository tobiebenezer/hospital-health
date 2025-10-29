<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AppointmentService
{
    public function getAvailableSlots(int $doctorId, string $date, int $duration): array
    {
        $doctor = Doctor::findOrFail($doctorId);
        $date = Carbon::parse($date);
        
        // Get doctor's schedule for the day
        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $date->dayOfWeek)
            ->first();
        
        if (!$schedule) {
            return [];
        }
        
        // Get existing appointments
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('start_time', $date)
            ->get();
        
        // Generate available slots
        $slots = [];
        $start = $date->copy()->setTimeFrom($schedule->start_time);
        $end = $date->copy()->setTimeFrom($schedule->end_time);
        
        $period = CarbonPeriod::create($start, $duration . ' minutes', $end);
        
        foreach ($period as $slotStart) {
            $slotEnd = $slotStart->copy()->addMinutes($duration);
            
            // Check if slot is available
            $conflict = $appointments->first(function ($appt) use ($slotStart, $slotEnd) {
                return $appt->start_time < $slotEnd && $appt->end_time > $slotStart;
            });
            
            if (!$conflict && $slotEnd <= $end) {
                $slots[] = [
                    'start' => $slotStart->toDateTimeString(),
                    'end' => $slotEnd->toDateTimeString()
                ];
            }
        }
        
        return $slots;
    }
    
    public function bookAppointment(array $data): Appointment
    {
        // Verify slot is still available
        $conflict = Appointment::where('doctor_id', $data['doctor_id'])
            ->where(function($query) use ($data) {
                $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                      ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']]);
            })
            ->exists();
            
        if ($conflict) {
            throw new \Exception('This time slot is no longer available');
        }
        
        return Appointment::create($data);
    }
}
