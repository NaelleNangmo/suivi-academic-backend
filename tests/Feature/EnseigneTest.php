<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Personnel;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Ue;
use App\Models\Ec;
use Laravel\Sanctum\Sanctum;

class EnseigneTest extends TestCase
{
    use RefreshDatabase;
    public function test_index_enseigne(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);

        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);

        $response = $this->get('/api/enseignes');
        $response->assertStatus(200);
    }

    //test pour verifier la creation d'une affectation
    public function test_create_enseigne(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $personnel2 = Personnel::factory()->create(['code_pers' => 'P-001']);
        $filiere = Filiere::factory()->create();
        $niveau = Niveau::factory()->create(['code_filiere' => $filiere->code_filiere]);
        $ue = Ue::factory()->create(['code_niveau' => $niveau->code_niveau]);
        $ec = Ec::factory()->create(['code_ec' => 'EC-001', 'code_ue' => $ue->code_ue]);
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
        $response = $this->post('/api/enseignes', [
            'code_pers' => $personnel2->code_pers,
            'code_ec' => $ec->code_ec
        ]);
        $response->assertStatus(201);
    }

    //test pour verifier l'affichage d'une affectation
    public function test_show_enseigne(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $personnel2 = Personnel::factory()->create(['code_pers' => 'P-002']);
        $filiere = Filiere::factory()->create();
        $niveau = Niveau::factory()->create(['code_filiere' => $filiere->code_filiere]);
        $ue = Ue::factory()->create(['code_niveau' => $niveau->code_niveau]);
        $ec = Ec::factory()->create(['code_ec' => 'EC-002', 'code_ue' => $ue->code_ue]);
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis affichage de l'affectation créée
        $response = $this->post('/api/enseignes', [
            'code_pers' => $personnel2->code_pers,
            'code_ec' => $ec->code_ec
        ]);
        $response->assertStatus(201);

        $response = $this->get('/api/enseignes/' . $personnel2->code_pers . '/' . $ec->code_ec);
        $response->assertStatus(200);
    }

    //test pour verifier la suppression d'une affectation
    public function test_delete_enseigne(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $personnel2 = Personnel::factory()->create(['code_pers' => 'P-003']);
        $filiere = Filiere::factory()->create();
        $niveau = Niveau::factory()->create(['code_filiere' => $filiere->code_filiere]);
        $ue = Ue::factory()->create(['code_niveau' => $niveau->code_niveau]);
        $ec = Ec::factory()->create(['code_ec' => 'EC-003', 'code_ue' => $ue->code_ue]);
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis suppression de l'affectation créée
        $response = $this->post('/api/enseignes', [
            'code_pers' => $personnel2->code_pers,
            'code_ec' => $ec->code_ec
        ]);
        $response->assertStatus(201);
        $response = $this->delete('/api/enseignes/' . $personnel2->code_pers . '/' . $ec->code_ec);
        $response->assertStatus(200);
    }
}