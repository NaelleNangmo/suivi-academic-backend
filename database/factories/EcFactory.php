<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ue;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ec>
 */
class EcFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code_ec'   => strtoupper($this->faker->unique()->bothify('EC-###')),
            'label_ec'  => $this->faker->words(3, true),
            'desc_ec'   => $this->faker->paragraph(),
            'nbh_ec'    => $this->faker->numberBetween(30, 120), // heures
            'nbc_ec'    => $this->faker->numberBetween(1, 4),   // crédits

            'code_ue'   => Ue::inRandomOrder()->first()->code_ue,
        ];
    }
}
