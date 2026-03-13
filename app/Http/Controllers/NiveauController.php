<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Services\ActionLogger;

class NiveauController extends Controller
{
    public function index()
    {
        ActionLogger::logAction('NIVEAU_INDEX', ['action' => 'Liste des niveaux consultée']);
        
        $niveaux = Cache::remember('niveaux.all', 3600, function () {
            return Niveau::all();
        });
        
        ActionLogger::logAction('NIVEAU_INDEX_SUCCESS', [
            'count' => $niveaux->count(),
            'from_cache' => Cache::has('niveaux.all')
        ]);
        
        return response()->json($niveaux, 200);
    }

    public function store(Request $request)
    {
        ActionLogger::logAction('NIVEAU_STORE_ATTEMPT', [
            'data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'label_niveau' => 'required|string|max:256',
            'desc_niveau' => 'required|string',
            'code_filiere' => 'required|string|max:20|exists:filiere,code_filiere'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('NIVEAU_STORE_VALIDATION_FAILED', [
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $niveau = Niveau::create($request->all());
            
            // Invalider le cache
            Cache::forget('niveaux.all');
            
            ActionLogger::logAction('NIVEAU_STORE_SUCCESS', [
                'niveau_code' => $niveau->code_niveau,
                'niveau_data' => $niveau->toArray()
            ]);
            
            return response()->json($niveau, 201);
        } catch (\Exception $e) {
            ActionLogger::logError('NIVEAU_STORE_FAILED', $e, [
                'data' => $request->all()
            ]);
            return response()->json(['message' => 'Erreur lors de la création'], 500);
        }
    }

    public function show($code_niveau)
    {
        ActionLogger::logAction('NIVEAU_SHOW', ['code_niveau' => $code_niveau]);
        
        $niveau = Cache::remember("niveau.{$code_niveau}", 3600, function () use ($code_niveau) {
            return Niveau::find($code_niveau);
        });

        if (!$niveau) {
            ActionLogger::logAction('NIVEAU_SHOW_NOT_FOUND', ['code_niveau' => $code_niveau], 'warning');
            return response()->json(['message' => 'Niveau non trouvé'], 404);
        }

        ActionLogger::logAction('NIVEAU_SHOW_SUCCESS', [
            'code_niveau' => $code_niveau,
            'from_cache' => Cache::has("niveau.{$code_niveau}")
        ]);

        return response()->json($niveau, 200);
    }

    public function update(Request $request, $code_niveau)
    {
        ActionLogger::logAction('NIVEAU_UPDATE_ATTEMPT', [
            'code_niveau' => $code_niveau,
            'data' => $request->all()
        ]);

        $niveau = Niveau::find($code_niveau);

        if (!$niveau) {
            ActionLogger::logAction('NIVEAU_UPDATE_NOT_FOUND', ['code_niveau' => $code_niveau], 'warning');
            return response()->json(['message' => 'Niveau non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'label_niveau' => 'sometimes|required|string|max:256',
            'desc_niveau' => 'sometimes|required|string',
            'code_filiere' => 'sometimes|required|string|max:20|exists:filiere,code_filiere'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('NIVEAU_UPDATE_VALIDATION_FAILED', [
                'code_niveau' => $code_niveau,
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $originalData = $niveau->toArray();

        try {
            $niveau->update($request->all());
            
            // Invalider le cache
            Cache::forget('niveaux.all');
            Cache::forget("niveau.{$code_niveau}");
            
            ActionLogger::logAction('NIVEAU_UPDATE_SUCCESS', [
                'code_niveau' => $code_niveau,
                'changes' => array_diff_assoc($niveau->toArray(), $originalData)
            ]);
            
            return response()->json($niveau, 200);
        } catch (\Exception $e) {
            ActionLogger::logError('NIVEAU_UPDATE_FAILED', $e, [
                'code_niveau' => $code_niveau,
                'data' => $request->all()
            ]);
            return response()->json(['message' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    public function destroy($code_niveau)
    {
        ActionLogger::logAction('NIVEAU_DELETE_ATTEMPT', ['code_niveau' => $code_niveau]);

        $niveau = Niveau::find($code_niveau);

        if (!$niveau) {
            ActionLogger::logAction('NIVEAU_DELETE_NOT_FOUND', ['code_niveau' => $code_niveau], 'warning');
            return response()->json(['message' => 'Niveau non trouvé'], 404);
        }

        $niveauData = $niveau->toArray();

        try {
            $niveau->delete();
            
            // Invalider le cache
            Cache::forget('niveaux.all');
            Cache::forget("niveau.{$code_niveau}");
            
            ActionLogger::logAction('NIVEAU_DELETE_SUCCESS', [
                'code_niveau' => $code_niveau,
                'deleted_data' => $niveauData
            ]);
            
            return response()->json(['message' => 'Niveau supprimé avec succès'], 200);
        } catch (\Exception $e) {
            ActionLogger::logError('NIVEAU_DELETE_FAILED', $e, [
                'code_niveau' => $code_niveau
            ]);
            return response()->json(['message' => 'Erreur lors de la suppression'], 500);
        }
    }
}
