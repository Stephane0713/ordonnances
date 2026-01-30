<?php

namespace Database\Factories;

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
            'patient_first_name' => $this->faker->firstName,
            'patient_last_name' => $this->faker->lastName,
            'patient_ssn' => $this->faker->numerify('##############'),
            'patient_contact_method' => $method = $this->faker->randomElement(['email', 'call', 'sms']),
            'patient_contact_value' => match ($method) {
                'email' => $this->faker->safeEmail(),
                'call', 'sms' => '0' . $this->faker->numerify('6########'),
            },
            'doctor_first_name' => $this->faker->firstName,
            'doctor_last_name' => $this->faker->lastName,
            'prescribed_at' => $prescribedAt,
            'validity_duration_in_months' => 12,
            'renewable_count' => 5,
            'dispensed_count' => $lastDispensed ? $this->faker->numberBetween(1, 5) : 0,
            'last_dispensed_at' => $lastDispensed,
            'dispense_interval_days' => $dispenseInterval,
            'notes' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['to_prepare', 'to_deliver', 'closed', 'waiting_for_consent']),
        ];
    }
}
