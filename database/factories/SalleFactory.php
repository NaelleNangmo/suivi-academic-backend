<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Salle>
 */
class SalleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'num_salle' => strtoupper($this->faker->unique()->bothify('SALLE-###')),
            'contenance' => $this->faker->numberBetween(20, 200),
            'statut'      => $this->faker->randomElement(['DISPONIBLE', 'NON DISPONIBLE']),
        ];
    }
}
