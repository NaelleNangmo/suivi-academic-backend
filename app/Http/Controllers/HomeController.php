<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Ue;
use App\Models\Ec;
use App\Models\Personnel;
use App\Models\Salle;
use App\Models\Programmation;
use App\Models\Enseigne;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'filieres'       => Filiere::count(),
            'niveaux'        => Niveau::count(),
            'ues'            => Ue::count(),
            'ecs'            => Ec::count(),
            'personnels'     => Personnel::count(),
            'salles'         => Salle::count(),
            'programmations' => Programmation::count(),
            'enseignes'      => Enseigne::count(),
        ];

        return view('home', compact('stats'));
    }
}
