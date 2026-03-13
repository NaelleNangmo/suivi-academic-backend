<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Personnel;
use App\Models\Filiere;
use App\Models\Niveau;
use Laravel\Sanctum\Sanctum;

class UeTest extends TestCase
{
    use RefreshDatabase;
    public function test_index_ue(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);

        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);

        $response = $this->get('/api/ues');
        $response->assertStatus(200);
    }

    //test pour verifier la creation d'une UE
    public function test_create_ue(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $filiere = Filiere::factory()->create();
        $niveau = Niveau::factory()->create(['code_filiere' => $filiere->code_filiere]);
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
        $response = $this->post('/api/ues', [
            'code_ue' => 'UE-999',
            'label_ue' => 'UE Test',
            'desc_ue' => 'Description de l\'UE test',
            'code_niveau' => $niveau->code_niveau
        ]);
        $response->assertStatus(201);
    }

    //test pour verifier l'update d'une UE
    public function test_update_ue(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $filiere = Filiere::factory()->create();
        $niveau = Niveau::factory()->create(['code_filiere' => $filiere->code_filiere]);
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis modification de l'UE créée
        $response = $this->post('/api/ues', [
            'code_ue' => 'UE-998',
            'label_ue' => 'UE Test',
            'desc_ue' => 'Description de l\'UE test',
            'code_niveau' => $niveau->code_niveau
        ]);
        $response->assertStatus(201);

        $response = $this->put('/api/ues/UE-998', [
            'label_ue' => 'UE Test Modifiée',
            'desc_ue' => 'Description modifiée de l\'UE test'
        ]);
        $response->assertStatus(200);
    }

    //test pour verifier la suppression d'une UE
    public function test_delete_ue(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $filiere = Filiere::factory()->create();
        $niveau = Niveau::factory()->create(['code_filiere' => $filiere->code_filiere]);
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis suppression de l'UE créée
        $response = $this->post('/api/ues', [
            'code_ue' => 'UE-997',
            'label_ue' => 'UE Test',
            'desc_ue' => 'Description de l\'UE test',
            'code_niveau' => $niveau->code_niveau
        ]);
        $response->assertStatus(201);
        $response = $this->delete('/api/ues/UE-997');
        $response->assertStatus(200);
    }
}