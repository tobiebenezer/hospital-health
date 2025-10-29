<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 12; $i++) {
            Patient::create([
                'firstname' => $faker->firstName(),
                'lastname' => $faker->lastName(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'phone' => $faker->optional()->phoneNumber(),
                'address' => $faker->optional()->address(),
                'occupation' => $faker->optional()->jobTitle(),
                'image' => $faker->optional()->imageUrl(200,200,'people', true),
                'gender' => $faker->randomElement(['Male','Female','Other']),
                'city' => $faker->city(),
                'state' => $faker->state(),
                'country' => $faker->country(),
                'status' => $faker->randomElement(['active','inactive']),
                'blood_group' => $faker->randomElement(['A+','A-','B+','B-','AB+','AB-','O+','O-']),
                'date_of_birth' => $faker->optional()->date(),
                'age' => $faker->numberBetween(1, 100),
                'height' => $faker->numberBetween(120, 210),
                'weight' => $faker->numberBetween(40, 150),
            ]);
        }
    }
}
