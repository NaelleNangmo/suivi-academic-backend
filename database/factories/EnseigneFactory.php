<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Personnel;
use App\Models\Ec;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enseigne>
 */
class EnseigneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         // Récupération des collections existantes
        $ec = Ec::all()->pluck('code_ec');
        $pers = Personnel::all()->pluck('code_pers');

        // Génération aléatoire unique
        do {
            $code_ec   = $ec->random();
            $code_pers = $pers->random();
        } while (\DB::table('enseigne')
            ->where('code_ec', $code_ec)
            ->where('code_pers', $code_pers)
            ->exists()
        );

        return [
            'code_ec'   => $code_ec,
            'code_pers' => $code_pers,
        ];
    }
}
