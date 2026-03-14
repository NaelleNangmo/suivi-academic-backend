<?php

namespace App\Http\Controllers;

use App\Models\Programmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Services\ActionLogger;

class ProgrammationController extends Controller
{
    public function index()
    {
        ActionLogger::logAction('PROGRAMMATION_INDEX', ['action' => 'Liste des programmations consultée']);
        
        $programmations = Cache::remember('programmations.all', 3600, function () {
            return Programmation::all();
        });
        
        ActionLogger::logAction('PROGRAMMATION_INDEX_SUCCESS', [
            'count' => $programmations->count(),
            'from_cache' => Cache::has('programmations.all')
        ]);
        
        return response()->json($programmations, 200);
    }

    public function store(Request $request)
    {
        ActionLogger::logAction('PROGRAMMATION_STORE_ATTEMPT', [
            'data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'code_ec' => 'required|string|max:20|exists:ec,code_ec',
            'num_salle' => 'required|string|exists:salle,num_salle',
            'code_pers' => 'required|string|max:20|exists:personnel,code_pers',
            'date' => 'required|date',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'nbre_heure' => 'required|integer|min:1',
            'statut' => 'required|in:EN COURS,EN ATTENTE,ACHEVER'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('PROGRAMMATION_STORE_VALIDATION_FAILED', [
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Vérifie si la programmation existe déjà
        $exists = Programmation::where('code_ec', $request->code_ec)
                               ->where('num_salle', $request->num_salle)
                               ->where('code_pers', $request->code_pers)
                               ->exists();

        if ($exists) {
            ActionLogger::logAction('PROGRAMMATION_STORE_ALREADY_EXISTS', [
                'code_ec' => $request->code_ec,
                'num_salle' => $request->num_salle,
                'code_pers' => $request->code_pers
            ], 'warning');
            return response()->json(['message' => 'Cette programmation existe déjà'], 409);
        }

        try {
            $programmation = Programmation::create($request->all());
            
            // Invalider le cache
            Cache::forget('programmations.all');
            
            ActionLogger::logAction('PROGRAMMATION_STORE_SUCCESS', [
                'programmation_data' => $programmation->toArray()
            ]);
            
            return response()->json($programmation, 201);
        } catch (\Exception $e) {
            ActionLogger::logError('PROGRAMMATION_STORE_FAILED', $e, [
                'data' => $request->all()
            ]);
            return response()->json(['message' => 'Erreur lors de la création'], 500);
        }
    }

    public function show($code_ec, $num_salle, $code_pers)
    {
        ActionLogger::logAction('PROGRAMMATION_SHOW', [
            'code_ec' => $code_ec,
            'num_salle' => $num_salle,
            'code_pers' => $code_pers
        ]);
        
        $cacheKey = "programmation.{$code_ec}.{$num_salle}.{$code_pers}";
        $programmation = Cache::remember($cacheKey, 3600, function () use ($code_ec, $num_salle, $code_pers) {
            return Programmation::where('code_ec', $code_ec)
                                ->where('num_salle', $num_salle)
                                ->where('code_pers', $code_pers)
                                ->first();
        });

        if (!$programmation) {
            ActionLogger::logAction('PROGRAMMATION_SHOW_NOT_FOUND', [
                'code_ec' => $code_ec,
                'num_salle' => $num_salle,
                'code_pers' => $code_pers
            ], 'warning');
            return response()->json(['message' => 'Programmation non trouvée'], 404);
        }

        ActionLogger::logAction('PROGRAMMATION_SHOW_SUCCESS', [
            'code_ec' => $code_ec,
            'num_salle' => $num_salle,
            'code_pers' => $code_pers,
            'from_cache' => Cache::has($cacheKey)
        ]);

        return response()->json($programmation, 200);
    }

    public function update(Request $request, $code_ec, $num_salle, $code_pers)
    {
        ActionLogger::logAction('PROGRAMMATION_UPDATE_ATTEMPT', [
            'code_ec' => $code_ec,
            'num_salle' => $num_salle,
            'code_pers' => $code_pers,
            'data' => $request->all()
        ]);

        $programmation = Programmation::where('code_ec', $code_ec)
                                      ->where('num_salle', $num_salle)
                                      ->where('code_pers', $code_pers)
                                      ->first();

        if (!$programmation) {
            ActionLogger::logAction('PROGRAMMATION_UPDATE_NOT_FOUND', [
                'code_ec' => $code_ec,
                'num_salle' => $num_salle,
                'code_pers' => $code_pers
            ], 'warning');
            return response()->json(['message' => 'Programmation non trouvée'], 404);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'sometimes|required|date',
            'date_debut' => 'sometimes|required|date',
            'date_fin' => 'sometimes|required|date',
            'nbre_heure' => 'sometimes|required|integer|min:1',
            'statut' => 'sometimes|required|in:EN COURS,EN ATTENTE,ACHEVER'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('PROGRAMMATION_UPDATE_VALIDATION_FAILED', [
                'code_ec' => $code_ec,
                'num_salle' => $num_salle,
                'code_pers' => $code_pers,
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $originalData = $programmation->toArray();

        try {
            $programmation->update($request->all());
            
            // Invalider le cache
            Cache::forget('programmations.all');
            Cache::forget("programmation.{$code_ec}.{$num_salle}.{$code_pers}");
            
            ActionLogger::logAction('PROGRAMMATION_UPDATE_SUCCESS', [
                'code_ec' => $code_ec,
                'num_salle' => $num_salle,
                'code_pers' => $code_pers,
                'changes' => array_diff_assoc($programmation->toArray(), $originalData)
            ]);
            
            return response()->json($programmation, 200);
        } catch (\Exception $e) {
            ActionLogger::logError('PROGRAMMATION_UPDATE_FAILED', $e, [
                'code_ec' => $code_ec,
                'num_salle' => $num_salle,
                'code_pers' => $code_pers,
                'data' => $request->all()
            ]);
            return response()->json(['message' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    public function destroy($code_ec, $num_salle, $code_pers)
    {
        ActionLogger::logAction('PROGRAMMATION_DELETE_ATTEMPT', [
            'code_ec' => $code_ec,
            'num_salle' => $num_salle,
            'code_pers' => $code_pers
        ]);

        $programmation = Programmation::where('code_ec', $code_ec)
                                      ->where('num_salle', $num_salle)
                                      ->where('code_pers', $code_pers)
                                      ->first();

        if (!$programmation) {
            ActionLogger::logAction('PROGRAMMATION_DELETE_NOT_FOUND', [
                'code_ec' => $code_ec,
                'num_salle' => $num_salle,
                'code_pers' => $code_pers
            ], 'warning');
            return response()->json(['message' => 'Programmation non trouvée'], 404);
        }

        $programmationData = $programmation->toArray();

        try {
            $programmation->delete();
            
            // Invalider le cache
            Cache::forget('programmations.all');
            Cache::forget("programmation.{$code_ec}.{$num_salle}.{$code_pers}");
            
            ActionLogger::logAction('PROGRAMMATION_DELETE_SUCCESS', [
                'code_ec' => $code_ec,
                'num_salle' => $num_salle,
                'code_pers' => $code_pers,
                'deleted_data' => $programmationData
            ]);
            
            return response()->json(['message' => 'Programmation supprimée avec succès'], 200);
        } catch (\Exception $e) {
            ActionLogger::logError('PROGRAMMATION_DELETE_FAILED', $e, [
                'code_ec' => $code_ec,
                'num_salle' => $num_salle,
                'code_pers' => $code_pers
            ]);
            return response()->json(['message' => 'Erreur lors de la suppression'], 500);
        }
    }
}
