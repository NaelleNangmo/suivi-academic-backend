<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Personnel>
 */
class PersonnelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code_pers'   => strtoupper($this->faker->unique()->bothify('PERS-###')),
            'nom_pers'    => $this->faker->lastName(),
            'prenom_pers' => $this->faker->firstName(),
            'sexe_pers'   => $this->faker->randomElement(['M', 'F']),
            'phone_pers'  => $this->faker->phoneNumber(),
            'login_pers'  => $this->faker->unique()->safeEmail(),
            'pwd_pers'    => bcrypt('password'),
            'type_pers'   => $this->faker->randomElement(['ENSEIGNANT','RESPONSABLE ACADEMIQUE','RESPONSABLE DISCIPLINE']),
        ];
    }
}
