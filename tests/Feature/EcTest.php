<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Personnel;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Ue;
use Laravel\Sanctum\Sanctum;

class EcTest extends TestCase
{
    use RefreshDatabase;
    public function test_index_ec(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);

        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);

        $response = $this->get('/api/ecs');
        $response->assertStatus(200);
    }

    //test pour verifier la creation d'un EC
    public function test_create_ec(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $filiere = Filiere::factory()->create();
        $niveau = Niveau::factory()->create(['code_filiere' => $filiere->code_filiere]);
        $ue = Ue::factory()->create(['code_niveau' => $niveau->code_niveau]);
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
        $response = $this->post('/api/ecs', [
            'code_ec' => 'EC-999',
            'label_ec' => 'EC Test',
            'desc_ec' => 'Description de l\'EC test',
            'nbh_ec' => 20,
            'nbc_ec' => 2,
            'code_ue' => $ue->code_ue
        ]);
        $response->assertStatus(201);
    }

    //test pour verifier l'update d'un EC
    public function test_update_ec(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $filiere = Filiere::factory()->create();
        $niveau = Niveau::factory()->create(['code_filiere' => $filiere->code_filiere]);
        $ue = Ue::factory()->create(['code_niveau' => $niveau->code_niveau]);
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis modification de l'EC créé
        $response = $this->post('/api/ecs', [
            'code_ec' => 'EC-998',
            'label_ec' => 'EC Test',
            'desc_ec' => 'Description de l\'EC test',
            'nbh_ec' => 20,
            'nbc_ec' => 2,
            'code_ue' => $ue->code_ue
        ]);
        $response->assertStatus(201);

        $response = $this->put('/api/ecs/EC-998', [
            'label_ec' => 'EC Test Modifié',
            'desc_ec' => 'Description modifiée de l\'EC test',
            'nbh_ec' => 25,
            'nbc_ec' => 3
        ]);
        $response->assertStatus(200);
    }

    //test pour verifier la suppression d'un EC
    public function test_delete_ec(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $filiere = Filiere::factory()->create();
        $niveau = Niveau::factory()->create(['code_filiere' => $filiere->code_filiere]);
        $ue = Ue::factory()->create(['code_niveau' => $niveau->code_niveau]);
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis suppression de l'EC créé
        $response = $this->post('/api/ecs', [
            'code_ec' => 'EC-997',
            'label_ec' => 'EC Test',
            'desc_ec' => 'Description de l\'EC test',
            'nbh_ec' => 20,
            'nbc_ec' => 2,
            'code_ue' => $ue->code_ue
        ]);
        $response->assertStatus(201);
        $response = $this->delete('/api/ecs/EC-997');
        $response->assertStatus(200);
    }
}