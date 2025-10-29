<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\LabReport;
use App\Models\Patient;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class LabReportSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $patients = Patient::query()->pluck('id');
        $doctors = Doctor::query()->pluck('id');

        if ($patients->isEmpty() || $doctors->isEmpty()) {
            return; // prerequisites not met
        }

        for ($i = 0; $i < 15; $i++) {
            LabReport::create([
                'patient_id' => $patients->random(),
                'doctor_id' => $doctors->random(),
                'report' => $faker->paragraphs(2, true),
                'status' => $faker->randomElement(['pending','completed','review']),
                'tests' => $faker->sentence(6),
            ]);
        }
    }
}
