<?php

namespace App\Http\Controllers;

use App\Models\Enseigne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Services\ActionLogger;

class EnseigneController extends Controller
{
    public function index()
    {
        ActionLogger::logAction('ENSEIGNE_INDEX', ['action' => 'Liste des enseignements consultée']);
        
        $enseignes = Cache::remember('enseignes.all', 3600, function () {
            return Enseigne::all();
        });
        
        ActionLogger::logAction('ENSEIGNE_INDEX_SUCCESS', [
            'count' => $enseignes->count(),
            'from_cache' => Cache::has('enseignes.all')
        ]);
        
        return response()->json($enseignes, 200);
    }

    public function store(Request $request)
    {
        ActionLogger::logAction('ENSEIGNE_STORE_ATTEMPT', [
            'data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'code_pers' => 'required|string|max:20|exists:personnel,code_pers',
            'code_ec' => 'required|string|max:20|exists:ec,code_ec'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('ENSEIGNE_STORE_VALIDATION_FAILED', [
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Vérifie si la relation existe déjà
        $exists = Enseigne::where('code_pers', $request->code_pers)
                          ->where('code_ec', $request->code_ec)
                          ->exists();

        if ($exists) {
            ActionLogger::logAction('ENSEIGNE_STORE_ALREADY_EXISTS', [
                'code_pers' => $request->code_pers,
                'code_ec' => $request->code_ec
            ], 'warning');
            return response()->json(['message' => 'Cette affectation existe déjà'], 409);
        }

        try {
            $enseigne = Enseigne::create($request->all());
            
            // Invalider le cache
            Cache::forget('enseignes.all');
            
            ActionLogger::logAction('ENSEIGNE_STORE_SUCCESS', [
                'enseigne_data' => $enseigne->toArray()
            ]);
            
            return response()->json($enseigne, 201);
        } catch (\Exception $e) {
            ActionLogger::logError('ENSEIGNE_STORE_FAILED', $e, [
                'data' => $request->all()
            ]);
            return response()->json(['message' => 'Erreur lors de la création'], 500);
        }
    }

    public function show($code_pers, $code_ec)
    {
        ActionLogger::logAction('ENSEIGNE_SHOW', [
            'code_pers' => $code_pers,
            'code_ec' => $code_ec
        ]);
        
        $cacheKey = "enseigne.{$code_pers}.{$code_ec}";
        $enseigne = Cache::remember($cacheKey, 3600, function () use ($code_pers, $code_ec) {
            return Enseigne::where('code_pers', $code_pers)
                           ->where('code_ec', $code_ec)
                           ->first();
        });

        if (!$enseigne) {
            ActionLogger::logAction('ENSEIGNE_SHOW_NOT_FOUND', [
                'code_pers' => $code_pers,
                'code_ec' => $code_ec
            ], 'warning');
            return response()->json(['message' => 'Affectation non trouvée'], 404);
        }

        ActionLogger::logAction('ENSEIGNE_SHOW_SUCCESS', [
            'code_pers' => $code_pers,
            'code_ec' => $code_ec,
            'from_cache' => Cache::has($cacheKey)
        ]);

        return response()->json($enseigne, 200);
    }

    public function destroy($code_pers, $code_ec)
    {
        ActionLogger::logAction('ENSEIGNE_DELETE_ATTEMPT', [
            'code_pers' => $code_pers,
            'code_ec' => $code_ec
        ]);

        $enseigne = Enseigne::where('code_pers', $code_pers)
                            ->where('code_ec', $code_ec)
                            ->first();

        if (!$enseigne) {
            ActionLogger::logAction('ENSEIGNE_DELETE_NOT_FOUND', [
                'code_pers' => $code_pers,
                'code_ec' => $code_ec
            ], 'warning');
            return response()->json(['message' => 'Affectation non trouvée'], 404);
        }

        $enseigneData = $enseigne->toArray();

        try {
            $enseigne->delete();
            
            // Invalider le cache
            Cache::forget('enseignes.all');
            Cache::forget("enseigne.{$code_pers}.{$code_ec}");
            
            ActionLogger::logAction('ENSEIGNE_DELETE_SUCCESS', [
                'code_pers' => $code_pers,
                'code_ec' => $code_ec,
                'deleted_data' => $enseigneData
            ]);
            
            return response()->json(['message' => 'Affectation supprimée avec succès'], 200);
        } catch (\Exception $e) {
            ActionLogger::logError('ENSEIGNE_DELETE_FAILED', $e, [
                'code_pers' => $code_pers,
                'code_ec' => $code_ec
            ]);
            return response()->json(['message' => 'Erreur lors de la suppression'], 500);
        }
    }
}
