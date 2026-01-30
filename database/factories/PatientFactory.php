<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'ssn' => $this->faker->numerify('##############'),
            'contact_method' => $method = $this->faker->randomElement(['email', 'call', 'sms']),
            'contact_value' => match ($method) {
                'email' => $this->faker->safeEmail(),
                'call', 'sms' => '0' . $this->faker->numerify('6########'),
            },
            'consent_file' => $this->faker->optional(0.9)->url(),
        ];
    }
}
