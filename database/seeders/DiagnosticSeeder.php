<?php

namespace Database\Seeders;

use App\Models\Diagnostic;
use App\Models\Doctor;
use App\Models\LabReport;
use App\Models\Patient;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DiagnosticSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $patients = Patient::query()->pluck('id');
        $doctors = Doctor::query()->pluck('id');
        $reports = LabReport::query()->pluck('id');

        if ($patients->isEmpty() || $doctors->isEmpty() || $reports->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 20; $i++) {
            Diagnostic::create([
                'prescription' => $faker->optional()->paragraph(),
                'report_id' => $reports->random(),
                'diagnostics' => $faker->sentence(10),
                'patient_id' => $patients->random(),
                'doctor_id' => $doctors->random(),
                'status' => $faker->randomElement(['active','resolved','pending']),
            ]);
        }
    }
}
