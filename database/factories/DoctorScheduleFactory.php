<?php

namespace Database\Factories;

use App\Models\DoctorSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorScheduleFactory extends Factory
{
    protected $model = DoctorSchedule::class;

    public function definition(): array
    {
        return [
            'doctor_id' => 1,
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_available' => true,
        ];
    }
}
