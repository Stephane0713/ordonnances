<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prescription>
 */
class PrescriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prescribedAt = $this->faker->dateTimeBetween('-2 month', 'now');
        $dispenseInterval = 28;

        $lastDispensed = $this->faker->optional()->dateTimeBetween($prescribedAt, 'now');

        return [
            'patient_id' => Patient::pluck('id')->random(),
            'doctor_first_name' => $this->faker->firstName,
            'doctor_last_name' => $this->faker->lastName,
            'prescribed_at' => $prescribedAt,
            'validity_duration_in_months' => 12,
            'renewable_count' => 5,
            'dispensed_count' => $lastDispensed ? $this->faker->numberBetween(1, 5) : 0,
            'last_dispensed_at' => $lastDispensed,
            'dispense_interval_days' => $dispenseInterval,
            'notes' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['to_prepare', 'to_deliver', 'closed']),
        ];
    }
}
