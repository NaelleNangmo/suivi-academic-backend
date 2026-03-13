<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Personnel;
use Laravel\Sanctum\Sanctum;

class SalleTest extends TestCase
{
    use RefreshDatabase;
    public function test_index_salle(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);

        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);

        $response = $this->get('/api/salles');
        $response->assertStatus(200);
    }

    //test pour verifier la creation d'une salle
    public function test_create_salle(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
        $response = $this->post('/api/salles', [
            'num_salle' => 'S-999',
            'contenance' => 50,
            'statut' => 'DISPONIBLE'
        ]);
        $response->assertStatus(201);
    }

    //test pour verifier l'update d'une salle
    public function test_update_salle(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis modification de la salle créée
        $response = $this->post('/api/salles', [
            'num_salle' => 'S-998',
            'contenance' => 40,
            'statut' => 'DISPONIBLE'
        ]);
        $response->assertStatus(201);

        $response = $this->put('/api/salles/S-998', [
            'contenance' => 60,
            'statut' => 'NON DISPONIBLE'
        ]);
        $response->assertStatus(200);
    }

    //test pour verifier la suppression d'une salle
    public function test_delete_salle(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis suppression de la salle créée
        $response = $this->post('/api/salles', [
            'num_salle' => 'S-997',
            'contenance' => 30,
            'statut' => 'DISPONIBLE'
        ]);
        $response->assertStatus(201);
        $response = $this->delete('/api/salles/S-997');
        $response->assertStatus(200);
    }
}