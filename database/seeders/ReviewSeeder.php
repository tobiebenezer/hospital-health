<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $doctorIds = Doctor::query()->pluck('id');
        $patientIds = Patient::query()->pluck('id');

        if ($doctorIds->isEmpty() || $patientIds->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 40; $i++) {
            Review::create([
                'auth_type' => Patient::class,
                'auth_id' => $patientIds->random(),
                'reviewable_type' => Doctor::class,
                'reviewable_id' => $doctorIds->random(),
                'rating' => $faker->numberBetween(1, 5),
                'comment' => $faker->optional()->sentence(12),
            ]);
        }
    }
}
