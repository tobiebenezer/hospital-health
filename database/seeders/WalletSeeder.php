<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Users
        foreach (User::all() as $user) {
            Wallet::firstOrCreate(
                ['walletable_type' => User::class, 'walletable_id' => $user->id],
                [
                    'balance' => $faker->numberBetween(0, 50000),
                    'currency' => $faker->randomElement(['USD','EUR','NGN']),
                ]
            );
        }

        // Doctors
        foreach (Doctor::all() as $doctor) {
            Wallet::firstOrCreate(
                ['walletable_type' => Doctor::class, 'walletable_id' => $doctor->id],
                [
                    'balance' => $faker->numberBetween(0, 50000),
                    'currency' => $faker->randomElement(['USD','EUR','NGN']),
                ]
            );
        }

        // Patients
        foreach (Patient::all() as $patient) {
            Wallet::firstOrCreate(
                ['walletable_type' => Patient::class, 'walletable_id' => $patient->id],
                [
                    'balance' => $faker->numberBetween(0, 50000),
                    'currency' => $faker->randomElement(['USD','EUR','NGN']),
                ]
            );
        }
    }
}
