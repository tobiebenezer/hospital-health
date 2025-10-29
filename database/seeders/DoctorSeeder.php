<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 8; $i++) {
            Doctor::create([
                'firstname' => $faker->firstName(),
                'lastname' => $faker->lastName(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'phone' => $faker->optional()->phoneNumber(),
                'address' => $faker->optional()->address(),
                'specialization' => $faker->randomElement(['Cardiology','Neurology','Pediatrics','Oncology','Dermatology']),
                'experience' => $faker->numberBetween(1, 30) . ' years',
                'education' => $faker->randomElement(['MBBS','MD','DO','PhD']),
                'image' => $faker->optional()->imageUrl(200,200,'people', true),
                'gender' => $faker->randomElement(['Male','Female','Other']),
                'city' => $faker->city(),
                'state' => $faker->state(),
                'country' => $faker->country(),
                'status' => $faker->randomElement(['active','inactive']),
                'bio' => $faker->optional()->paragraph(),
                'date_of_birth' => $faker->optional()->date(),
                'department' => $faker->randomElement(['General','Surgery','Emergency','Radiology']),
            ]);
        }
    }
}
