<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $patientIds = Patient::query()->pluck('id');
        $doctorIds = Doctor::query()->pluck('id');

        if ($patientIds->isEmpty() && $doctorIds->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 30; $i++) {
            // randomly choose paymentable type
            $isPatient = $faker->boolean(60);
            if ($isPatient && $patientIds->isNotEmpty()) {
                $type = Patient::class;
                $id = $patientIds->random();
            } elseif ($doctorIds->isNotEmpty()) {
                $type = Doctor::class;
                $id = $doctorIds->random();
            } else {
                continue;
            }

            Payment::create([
                'paymentable_type' => $type,
                'paymentable_id' => $id,
                'payment_method' => $faker->randomElement(['cash','card','transfer']),
                'amount' => $faker->numberBetween(1000, 200000),
                'currency' => $faker->randomElement(['NGN']),
                'payment_status' => $faker->randomElement(['pending','paid','failed']),
                'transaction_id' => strtoupper($faker->bothify('TX-########')),
                'payment_date' => $faker->optional()->dateTimeBetween('-1 year', 'now'),
                'meta' => [
                    'note' => $faker->optional()->sentence(),
                ],
            ]);
        }
    }
}
