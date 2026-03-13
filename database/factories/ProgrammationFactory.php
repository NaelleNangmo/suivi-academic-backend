<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ec;
use App\Models\Salle;
use App\Models\Personnel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Programmation>
 */
class ProgrammationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       // Récupération aléatoire des clés existantes
        $code_ec   = Ec::inRandomOrder()->value('code_ec');
        $num_salle = Salle::inRandomOrder()->value('num_salle');
        $code_pers = Personnel::inRandomOrder()->value('code_pers');

        // Création d’un créneau horaire cohérent
        $start = $this->faker->dateTimeBetween('+0 days', '+30 days');
        $end   = (clone $start)->modify('+'.rand(1, 3).' hours');

        // Boucle pour éviter doublons sur la clé composite
        while (\DB::table('programmation')
            ->where('code_ec', $code_ec)
            ->where('num_salle', $num_salle)
            ->where('code_pers', $code_pers)
            ->exists()
        ) {
            $code_ec   = Ec::inRandomOrder()->value('code_ec');
            $num_salle = Salle::inRandomOrder()->value('num_salle');
            $code_pers = Personnel::inRandomOrder()->value('code_pers');
        }

        return [
            'code_ec'    => $code_ec,
            'num_salle'  => $num_salle,
            'code_pers'  => $code_pers,
            'date'       => $start->format('Y-m-d'),
            'date-debut' => $start->format('Y-m-d H:i:s'), // ou renommer date_debut dans ta migration
            'date_fin'   => $end->format('Y-m-d H:i:s'),
            'nbre_heure' => $start->diff($end)->h,
            'statut'     => $this->faker->randomElement(['EN COURS', 'EN ATTENTE', 'ACHEVER']),
        ];
    }
}
