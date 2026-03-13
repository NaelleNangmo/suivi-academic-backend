<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Personnel;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Ue;
use App\Models\Ec;
use App\Models\Salle;
use Laravel\Sanctum\Sanctum;

class ProgrammationTest extends TestCase
{
    use RefreshDatabase;
    public function test_index_programmation(): void
    {
        // Créer un personnel pour l'authentification
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);

        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);

        $response = $this->get('/api/programmations');
        $response->assertStatus(200);
    }

    //test pour verifier la creation d'une programmation
    public function test_create_programmation(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $personnel2 = Personnel::factory()->create(['code_pers' => 'P-001']);
        $filiere = Filiere::factory()->create();
        $niveau = Niveau::factory()->create(['code_filiere' => $filiere->code_filiere]);
        $ue = Ue::factory()->create(['code_niveau' => $niveau->code_niveau]);
        $ec = Ec::factory()->create(['code_ec' => 'EC-001', 'code_ue' => $ue->code_ue]);
        $salle = Salle::factory()->create(['num_salle' => 'S-001']);
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
        $response = $this->post('/api/programmations', [
            'code_ec' => $ec->code_ec,
            'num_salle' => $salle->num_salle,
            'code_pers' => $personnel2->code_pers,
            'date' => '2024-01-15',
            'date-debut' => '2024-01-15 08:00:00',
            'date_fin' => '2024-01-15 10:00:00',
            'nbre_heure' => 2,
            'statut' => 'EN ATTENTE'
        ]);
        $response->assertStatus(201);
    }

    //test pour verifier l'affichage d'une programmation
    public function test_show_programmation(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $personnel2 = Personnel::factory()->create(['code_pers' => 'P-002']);
        $filiere = Filiere::factory()->create();
        $niveau = Niveau::factory()->create(['code_filiere' => $filiere->code_filiere]);
        $ue = Ue::factory()->create(['code_niveau' => $niveau->code_niveau]);
        $ec = Ec::factory()->create(['code_ec' => 'EC-002', 'code_ue' => $ue->code_ue]);
        $salle = Salle::factory()->create(['num_salle' => 'S-002']);
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis affichage de la programmation créée
        $response = $this->post('/api/programmations', [
            'code_ec' => $ec->code_ec,
            'num_salle' => $salle->num_salle,
            'code_pers' => $personnel2->code_pers,
            'date' => '2024-01-16',
            'date-debut' => '2024-01-16 08:00:00',
            'date_fin' => '2024-01-16 10:00:00',
            'nbre_heure' => 2,
            'statut' => 'EN ATTENTE'
        ]);
        $response->assertStatus(201);

        $response = $this->get('/api/programmations/' . $ec->code_ec . '/' . $salle->num_salle . '/' . $personnel2->code_pers);
        $response->assertStatus(200);
    }

    //test pour verifier l'update d'une programmation
    public function test_update_programmation(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $personnel2 = Personnel::factory()->create(['code_pers' => 'P-003']);
        $filiere = Filiere::factory()->create();
        $niveau = Niveau::factory()->create(['code_filiere' => $filiere->code_filiere]);
        $ue = Ue::factory()->create(['code_niveau' => $niveau->code_niveau]);
        $ec = Ec::factory()->create(['code_ec' => 'EC-003', 'code_ue' => $ue->code_ue]);
        $salle = Salle::factory()->create(['num_salle' => 'S-003']);
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis modification de la programmation créée
        $response = $this->post('/api/programmations', [
            'code_ec' => $ec->code_ec,
            'num_salle' => $salle->num_salle,
            'code_pers' => $personnel2->code_pers,
            'date' => '2024-01-17',
            'date-debut' => '2024-01-17 08:00:00',
            'date_fin' => '2024-01-17 10:00:00',
            'nbre_heure' => 2,
            'statut' => 'EN ATTENTE'
        ]);
        $response->assertStatus(201);

        $response = $this->put('/api/programmations/' . $ec->code_ec . '/' . $salle->num_salle . '/' . $personnel2->code_pers, [
            'statut' => 'EN COURS',
            'nbre_heure' => 3
        ]);
        $response->assertStatus(200);
    }

    //test pour verifier la suppression d'une programmation
    public function test_delete_programmation(): void
    {
        // Créer les données nécessaires
        $personnel = Personnel::factory()->create(['login_pers' => '3Na']);
        $personnel2 = Personnel::factory()->create(['code_pers' => 'P-004']);
        $filiere = Filiere::factory()->create();
        $niveau = Niveau::factory()->create(['code_filiere' => $filiere->code_filiere]);
        $ue = Ue::factory()->create(['code_niveau' => $niveau->code_niveau]);
        $ec = Ec::factory()->create(['code_ec' => 'EC-004', 'code_ue' => $ue->code_ue]);
        $salle = Salle::factory()->create(['num_salle' => 'S-004']);
        
        // Authentifie avec Sanctum
        Sanctum::actingAs($personnel);
       //creation puis suppression de la programmation créée
        $response = $this->post('/api/programmations', [
            'code_ec' => $ec->code_ec,
            'num_salle' => $salle->num_salle,
            'code_pers' => $personnel2->code_pers,
            'date' => '2024-01-18',
            'date-debut' => '2024-01-18 08:00:00',
            'date_fin' => '2024-01-18 10:00:00',
            'nbre_heure' => 2,
            'statut' => 'EN ATTENTE'
        ]);
        $response->assertStatus(201);
        $response = $this->delete('/api/programmations/' . $ec->code_ec . '/' . $salle->num_salle . '/' . $personnel2->code_pers);
        $response->assertStatus(200);
    }
}