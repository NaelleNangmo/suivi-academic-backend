<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Ue;

class WebViewController extends Controller
{
    public function filieres()       { return view('pages.filieres'); }
    public function niveaux()        { return view('pages.niveaux',    ['filieres' => Filiere::all()]); }
    public function ues()            { return view('pages.ues',        ['niveaux'  => Niveau::all()]); }
    public function ecs()            { return view('pages.ecs',        ['ues'      => Ue::all()]); }
    public function personnels()     { return view('pages.personnels'); }
    public function salles()         { return view('pages.salles'); }
    public function programmations() {
        return view('pages.programmations', [
            'ecs'        => \App\Models\Ec::all(),
            'salles'     => \App\Models\Salle::all(),
            'personnels' => \App\Models\Personnel::all(),
        ]);
    }
    public function enseignes() {
        return view('pages.enseignes', [
            'personnels' => \App\Models\Personnel::all(),
            'ecs'        => \App\Models\Ec::all(),
        ]);
    }
    public function docs() { return view('pages.docs'); }
}
