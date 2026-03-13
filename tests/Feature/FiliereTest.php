<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Personnel;
use Laravel\Sanctum\Sanctum;

class FiliereTest extends TestCase
{
    use RefreshDatabase;
    public function test_example(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);

        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);

        $response = $this->get('/api/filieres');
        $response->assertStatus(200);
    }

    //test pour verifier la creation d'une filiere
    public function test_create_filiere(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
        $response = $this->post('/api/filieres', [
            'code_filiere' => 'FI-999',
            'label_filiere' => 'Filiere Test',
            'desc_filiere' => 'Description de la filiere test',
        ]);
        $response->assertStatus(201);
    }


    //test pour verifier l'update d'une filiere
    public function test_update_filiere(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis modification de la filiere creer
        $response = $this->post('/api/filieres', [
            'code_filiere' => 'FI-998',
            'label_filiere' => 'Filiere Test',
            'desc_filiere' => 'Description de la filiere test',
        ]);
        $response->assertStatus(201);

        $response = $this->put('/api/filieres/FI-998', [
            'label_filiere' => 'Filiere Test Modifiee',
            'desc_filiere' => 'Description modifiee de la filiere test',
        ]);
        $response->assertStatus(200);
    }

    //test pour verifier la suppression d'une filiere
    public function test_delete_filiere(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis suppression de la filiere creer
        $response = $this->post('/api/filieres', [
            'code_filiere' => 'FI-997',
            'label_filiere' => 'Filiere Test',
            'desc_filiere' => 'Description de la filiere test',
        ]);
        $response->assertStatus(201);
        $response = $this->delete('/api/filieres/FI-997');
        $response->assertStatus(200);
    }
}
