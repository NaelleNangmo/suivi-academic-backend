<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Personnel;
use Laravel\Sanctum\Sanctum;

class PersonnelTest extends TestCase
{
    use RefreshDatabase;
    public function test_index_personnel(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);

        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);

        $response = $this->get('/api/personnels');
        $response->assertStatus(200);
    }

    //test pour verifier la creation d'un personnel
    public function test_create_personnel(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
        $response = $this->post('/api/personnels', [
            'code_pers' => 'P-999',
            'nom_pers' => 'Test',
            'prenom_pers' => 'Personnel',
            'sexe_pers' => 'M',
            'phone_pers' => '0123456789',
            'login_pers' => 'test999',
            'pwd_pers' => 'password123',
            'type_pers' => 'ENSEIGNANT'
        ]);
        $response->assertStatus(201);
    }

    //test pour verifier l'update d'un personnel
    public function test_update_personnel(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis modification du personnel créé
        $response = $this->post('/api/personnels', [
            'code_pers' => 'P-998',
            'nom_pers' => 'Test',
            'prenom_pers' => 'Personnel',
            'sexe_pers' => 'F',
            'phone_pers' => '0123456789',
            'login_pers' => 'test998',
            'pwd_pers' => 'password123',
            'type_pers' => 'ENSEIGNANT'
        ]);
        $response->assertStatus(201);

        $response = $this->put('/api/personnels/P-998', [
            'nom_pers' => 'Test Modifié',
            'prenom_pers' => 'Personnel Modifié',
            'phone_pers' => '0987654321'
        ]);
        $response->assertStatus(200);
    }

    //test pour verifier la suppression d'un personnel
    public function test_delete_personnel(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis suppression du personnel créé
        $response = $this->post('/api/personnels', [
            'code_pers' => 'P-997',
            'nom_pers' => 'Test',
            'prenom_pers' => 'Personnel',
            'sexe_pers' => 'M',
            'phone_pers' => '0123456789',
            'login_pers' => 'test997',
            'pwd_pers' => 'password123',
            'type_pers' => 'ENSEIGNANT'
        ]);
        $response->assertStatus(201);
        $response = $this->delete('/api/personnels/P-997');
        $response->assertStatus(200);
    }
}