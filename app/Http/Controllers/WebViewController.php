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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class WebViewController extends Controller
{
    // ─── VUES ────────────────────────────────────────────────────────────────

    public function filieres()
    {
        return view('pages.filieres', ['filieres' => Filiere::all()]);
    }

    public function niveaux()
    {
        return view('pages.niveaux', [
            'niveaux'  => Niveau::with('filiere')->get(),
            'filieres' => Filiere::all(),
        ]);
    }

    public function ues()
    {
        return view('pages.ues', [
            'ues'     => Ue::with('niveau')->get(),
            'niveaux' => Niveau::all(),
        ]);
    }

    public function ecs()
    {
        return view('pages.ecs', [
            'ecs' => Ec::with('ue')->get(),
            'ues' => Ue::all(),
        ]);
    }

    public function personnels()
    {
        return view('pages.personnels', ['personnels' => Personnel::all()]);
    }

    public function salles()
    {
        return view('pages.salles', ['salles' => Salle::all()]);
    }

    public function programmations()
    {
        return view('pages.programmations', [
            'programmations' => Programmation::all(),
            'ecs'            => Ec::all(),
            'salles'         => Salle::all(),
            'personnels'     => Personnel::all(),
        ]);
    }

    public function enseignes()
    {
        return view('pages.enseignes', [
            'enseignes'  => Enseigne::with(['personnel', 'ec'])->get(),
            'personnels' => Personnel::all(),
            'ecs'        => Ec::all(),
        ]);
    }

    public function docs() { return view('pages.docs'); }

    // ─── FILIÈRES ────────────────────────────────────────────────────────────

    public function filieresStore(Request $request)
    {
        $data = $request->validate([
            'code_filiere'  => 'required|string|max:20|unique:filiere,code_filiere',
            'label_filiere' => 'required|string|max:256',
            'desc_filiere'  => 'required|string',
        ]);
        Filiere::create($data);
        Cache::forget('filieres.all');
        return back()->with('success', 'Filière créée avec succès.');
    }

    public function filieresUpdate(Request $request, $id)
    {
        $filiere = Filiere::findOrFail($id);
        $data = $request->validate([
            'label_filiere' => 'required|string|max:256',
            'desc_filiere'  => 'required|string',
        ]);
        $filiere->update($data);
        Cache::forget('filieres.all');
        Cache::forget("filiere.{$id}");
        return back()->with('success', 'Filière mise à jour.');
    }

    public function filieresDestroy($id)
    {
        Filiere::findOrFail($id)->delete();
        Cache::forget('filieres.all');
        Cache::forget("filiere.{$id}");
        return back()->with('success', 'Filière supprimée.');
    }

    // ─── NIVEAUX ─────────────────────────────────────────────────────────────

    public function niveauxStore(Request $request)
    {
        $data = $request->validate([
            'label_niveau' => 'required|string|max:256',
            'desc_niveau'  => 'required|string',
            'code_filiere' => 'required|string|exists:filiere,code_filiere',
        ]);
        Niveau::create($data);
        Cache::forget('niveaux.all');
        return back()->with('success', 'Niveau créé avec succès.');
    }

    public function niveauxUpdate(Request $request, $id)
    {
        $niveau = Niveau::findOrFail($id);
        $data = $request->validate([
            'label_niveau' => 'required|string|max:256',
            'desc_niveau'  => 'required|string',
            'code_filiere' => 'required|string|exists:filiere,code_filiere',
        ]);
        $niveau->update($data);
        Cache::forget('niveaux.all');
        Cache::forget("niveau.{$id}");
        return back()->with('success', 'Niveau mis à jour.');
    }

    public function niveauxDestroy($id)
    {
        Niveau::findOrFail($id)->delete();
        Cache::forget('niveaux.all');
        Cache::forget("niveau.{$id}");
        return back()->with('success', 'Niveau supprimé.');
    }

    // ─── UE ──────────────────────────────────────────────────────────────────

    public function uesStore(Request $request)
    {
        $data = $request->validate([
            'code_ue'      => 'required|string|max:20|unique:ue,code_ue',
            'label_ue'     => 'required|string',
            'desc_ue'      => 'required|string',
            'code_niveau'  => 'required|integer|exists:niveau,code_niveau',
        ]);
        Ue::create($data);
        Cache::forget('ues.all');
        return back()->with('success', 'UE créée avec succès.');
    }

    public function uesUpdate(Request $request, $id)
    {
        $ue = Ue::findOrFail($id);
        $data = $request->validate([
            'label_ue'    => 'required|string',
            'desc_ue'     => 'required|string',
            'code_niveau' => 'required|integer|exists:niveau,code_niveau',
        ]);
        $ue->update($data);
        Cache::forget('ues.all');
        Cache::forget("ue.{$id}");
        return back()->with('success', 'UE mise à jour.');
    }

    public function uesDestroy($id)
    {
        Ue::findOrFail($id)->delete();
        Cache::forget('ues.all');
        Cache::forget("ue.{$id}");
        return back()->with('success', 'UE supprimée.');
    }

    // ─── EC ──────────────────────────────────────────────────────────────────

    public function ecsStore(Request $request)
    {
        $data = $request->validate([
            'code_ec'      => 'required|string|max:20|unique:ec,code_ec',
            'label_ec'     => 'required|string',
            'desc_ec'      => 'required|string',
            'nbh_ec'       => 'required|integer|min:1',
            'nbc_ec'       => 'required|integer|min:1',
            'code_ue'      => 'required|string|exists:ue,code_ue',
            'support_cours'=> 'nullable|file|mimes:pdf|max:10240',
        ]);
        if ($request->hasFile('support_cours')) {
            $file = $request->file('support_cours');
            $data['support_cours'] = $file->storeAs('cours', time().'_'.$file->getClientOriginalName(), 'public');
        }
        Ec::create($data);
        Cache::forget('ecs.all');
        return back()->with('success', 'EC créé avec succès.');
    }

    public function ecsUpdate(Request $request, $id)
    {
        $ec = Ec::findOrFail($id);
        $data = $request->validate([
            'label_ec' => 'required|string',
            'desc_ec'  => 'required|string',
            'nbh_ec'   => 'required|integer|min:1',
            'nbc_ec'   => 'required|integer|min:1',
            'code_ue'  => 'required|string|exists:ue,code_ue',
            'support_cours' => 'nullable|file|mimes:pdf|max:10240',
        ]);
        if ($request->hasFile('support_cours')) {
            if ($ec->support_cours) Storage::disk('public')->delete($ec->support_cours);
            $file = $request->file('support_cours');
            $data['support_cours'] = $file->storeAs('cours', time().'_'.$file->getClientOriginalName(), 'public');
        }
        $ec->update($data);
        Cache::forget('ecs.all');
        Cache::forget("ec.{$id}");
        return back()->with('success', 'EC mis à jour.');
    }

    public function ecsDestroy($id)
    {
        $ec = Ec::findOrFail($id);
        if ($ec->support_cours) Storage::disk('public')->delete($ec->support_cours);
        $ec->delete();
        Cache::forget('ecs.all');
        Cache::forget("ec.{$id}");
        return back()->with('success', 'EC supprimé.');
    }

    // ─── PERSONNEL ───────────────────────────────────────────────────────────

    public function personnelsStore(Request $request)
    {
        $data = $request->validate([
            'code_pers'   => 'required|string|unique:personnel,code_pers',
            'nom_pers'    => 'required|string',
            'prenom_pers' => 'nullable|string',
            'sexe_pers'   => 'required|in:M,F',
            'phone_pers'  => 'required|string',
            'login_pers'  => 'required|email|unique:personnel,login_pers',
            'pwd_pers'    => 'required|string|min:6',
            'type_pers'   => 'required|in:ENSEIGNANT,RESPONSABLE ACADEMIQUE,RESPONSABLE DISCIPLINE',
        ]);
        $data['pwd_pers'] = Hash::make($data['pwd_pers']);
        Personnel::create($data);
        Cache::forget('personnels.all');
        return back()->with('success', 'Membre créé avec succès.');
    }

    public function personnelsUpdate(Request $request, $id)
    {
        $personnel = Personnel::findOrFail($id);
        $data = $request->validate([
            'nom_pers'    => 'required|string',
            'prenom_pers' => 'nullable|string',
            'sexe_pers'   => 'required|in:M,F',
            'phone_pers'  => 'required|string',
            'login_pers'  => 'required|email|unique:personnel,login_pers,'.$id.',code_pers',
            'pwd_pers'    => 'nullable|string|min:6',
            'type_pers'   => 'required|in:ENSEIGNANT,RESPONSABLE ACADEMIQUE,RESPONSABLE DISCIPLINE',
        ]);
        if (empty($data['pwd_pers'])) unset($data['pwd_pers']);
        else $data['pwd_pers'] = Hash::make($data['pwd_pers']);
        $personnel->update($data);
        Cache::forget('personnels.all');
        Cache::forget("personnel.{$id}");
        return back()->with('success', 'Membre mis à jour.');
    }

    public function personnelsDestroy($id)
    {
        Personnel::findOrFail($id)->delete();
        Cache::forget('personnels.all');
        Cache::forget("personnel.{$id}");
        return back()->with('success', 'Membre supprimé.');
    }

    // ─── SALLES ──────────────────────────────────────────────────────────────

    public function sallesStore(Request $request)
    {
        $data = $request->validate([
            'num_salle'  => 'required|string|unique:salle,num_salle',
            'contenance' => 'required|integer|min:1',
            'statut'     => 'required|in:DISPONIBLE,NON DISPONIBLE',
        ]);
        Salle::create($data);
        Cache::forget('salles.all');
        return back()->with('success', 'Salle créée avec succès.');
    }

    public function sallesUpdate(Request $request, $id)
    {
        $salle = Salle::findOrFail($id);
        $data = $request->validate([
            'contenance' => 'required|integer|min:1',
            'statut'     => 'required|in:DISPONIBLE,NON DISPONIBLE',
        ]);
        $salle->update($data);
        Cache::forget('salles.all');
        Cache::forget("salle.{$id}");
        return back()->with('success', 'Salle mise à jour.');
    }

    public function sallesDestroy($id)
    {
        Salle::findOrFail($id)->delete();
        Cache::forget('salles.all');
        Cache::forget("salle.{$id}");
        return back()->with('success', 'Salle supprimée.');
    }

    // ─── PROGRAMMATIONS ──────────────────────────────────────────────────────

    public function programmationsStore(Request $request)
    {
        $data = $request->validate([
            'code_ec'    => 'required|string|exists:ec,code_ec',
            'num_salle'  => 'required|string|exists:salle,num_salle',
            'code_pers'  => 'required|string|exists:personnel,code_pers',
            'date'       => 'required|date',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
            'nbre_heure' => 'required|integer|min:1',
            'statut'     => 'required|in:EN COURS,EN ATTENTE,ACHEVER',
        ]);
        Programmation::create($data);
        Cache::forget('programmations.all');
        return back()->with('success', 'Séance créée avec succès.');
    }

    public function programmationsUpdate(Request $request, $ec, $salle, $pers)
    {
        $prog = Programmation::where('code_ec', $ec)->where('num_salle', $salle)->where('code_pers', $pers)->firstOrFail();
        $data = $request->validate([
            'date'       => 'required|date',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date',
            'nbre_heure' => 'required|integer|min:1',
            'statut'     => 'required|in:EN COURS,EN ATTENTE,ACHEVER',
        ]);
        $prog->update($data);
        Cache::forget('programmations.all');
        return back()->with('success', 'Séance mise à jour.');
    }

    public function programmationsDestroy($ec, $salle, $pers)
    {
        Programmation::where('code_ec', $ec)->where('num_salle', $salle)->where('code_pers', $pers)->firstOrFail()->delete();
        Cache::forget('programmations.all');
        return back()->with('success', 'Séance supprimée.');
    }

    // ─── ENSEIGNES ───────────────────────────────────────────────────────────

    public function enseignesStore(Request $request)
    {
        $data = $request->validate([
            'code_pers' => 'required|string|exists:personnel,code_pers',
            'code_ec'   => 'required|string|exists:ec,code_ec',
        ]);
        if (Enseigne::where('code_pers', $data['code_pers'])->where('code_ec', $data['code_ec'])->exists()) {
            return back()->with('error', 'Cette affectation existe déjà.');
        }
        Enseigne::create($data);
        Cache::forget('enseignes.all');
        return back()->with('success', 'Affectation créée.');
    }

    public function enseignesDestroy($pers, $ec)
    {
        Enseigne::where('code_pers', $pers)->where('code_ec', $ec)->firstOrFail()->delete();
        Cache::forget('enseignes.all');
        return back()->with('success', 'Affectation supprimée.');
    }
}
