<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Filiere;
use App\Models\Salle;
use App\Models\Niveau;
use App\Models\Personnel;
use App\Models\Ue;
use App\Models\Ec;
use App\Models\Enseigne;
use App\Models\Programmation;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Filiere::factory(200)->create();

        Niveau::factory(500)->create();

        Salle::factory(1000)->create();

        Personnel::factory(500)->create();

        Ue::factory(500)->create();

        Ec::factory(800)->create();

        Enseigne::factory(1000)->create();

        Programmation::factory(1500)->create();

    }
}
