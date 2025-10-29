<?php

namespace Tests\Feature;

use Database\Factories\DoctorFactory;
use Database\Factories\PatientFactory;
use Database\Factories\DoctorScheduleFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_doctor_availability()
    {
        $doctor = DoctorFactory::new()->create();
        DoctorScheduleFactory::new()->create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1, // Monday
            'start_time' => '09:00',
            'end_time' => '17:00'
        ]);

        $response = $this->getJson("/api/doctors/{$doctor->id}/availability?date=2025-10-27&duration=30");

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['start', 'end']
            ]);
    }

    public function test_book_appointment()
    {
        $doctor = DoctorFactory::new()->create();
        $patient = PatientFactory::new()->create();
        
        DoctorScheduleFactory::new()->create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '17:00'
        ]);

        $data = [
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'start_time' => '2025-10-27 10:00:00',
            'end_time' => '2025-10-27 10:30:00'
        ];

        $response = $this->postJson('/api/appointments', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'doctor_id', 'patient_id', 'start_time', 'end_time'
            ]);
    }
}
