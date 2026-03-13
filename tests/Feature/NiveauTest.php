<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Personnel;
use App\Models\Filiere;
use Laravel\Sanctum\Sanctum;

class NiveauTest extends TestCase
{
    use RefreshDatabase;
    public function test_index_niveau(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);

        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);

        $response = $this->get('/api/niveaux');
        $response->assertStatus(200);
    }

    //test pour verifier la creation d'un niveau
    public function test_create_niveau(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $filiere = Filiere::factory()->create();
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
        $response = $this->post('/api/niveaux', [
            'label_niveau' => 'Niveau Test',
            'desc_niveau' => 'Description du niveau test',
            'code_filiere' => $filiere->code_filiere
        ]);
        $response->assertStatus(201);
    }

    //test pour verifier l'update d'un niveau
    public function test_update_niveau(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $filiere = Filiere::factory()->create();
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis modification du niveau créé
        $response = $this->post('/api/niveaux', [
            'label_niveau' => 'Niveau Test 2',
            'desc_niveau' => 'Description du niveau test 2',
            'code_filiere' => $filiere->code_filiere
        ]);
        $response->assertStatus(201);
        
        // Récupérer l'ID du niveau créé
        $niveau = $response->json();
        $code_niveau = $niveau['code_niveau'];

        $response = $this->put('/api/niveaux/' . $code_niveau, [
            'label_niveau' => 'Niveau Test Modifié',
            'desc_niveau' => 'Description modifiée du niveau test'
        ]);
        $response->assertStatus(200);
    }

    //test pour verifier la suppression d'un niveau
    public function test_delete_niveau(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $filiere = Filiere::factory()->create();
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis suppression du niveau créé
        $response = $this->post('/api/niveaux', [
            'label_niveau' => 'Niveau Test 3',
            'desc_niveau' => 'Description du niveau test 3',
            'code_filiere' => $filiere->code_filiere
        ]);
        $response->assertStatus(201);
        
        // Récupérer l'ID du niveau créé
        $niveau = $response->json();
        $code_niveau = $niveau['code_niveau'];
        
        $response = $this->delete('/api/niveaux/' . $code_niveau);
        $response->assertStatus(200);
    }
}