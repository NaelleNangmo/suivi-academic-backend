<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Services\ActionLogger;

class FiliereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ActionLogger::logAction('FILIERE_INDEX', ['action' => 'Liste des filières consultée']);
        
        $filieres = Cache::remember('filieres.all', 3600, function () {
            return Filiere::all();
        });
        
        ActionLogger::logAction('FILIERE_INDEX_SUCCESS', [
            'count' => $filieres->count(),
            'from_cache' => Cache::has('filieres.all')
        ]);
        
        return response()->json($filieres, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        ActionLogger::logAction('FILIERE_STORE_ATTEMPT', [
            'data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'code_filiere' => 'required|string|max:20|unique:filiere,code_filiere',
            'label_filiere' => 'required|string|max:256',
            'desc_filiere' => 'required|string'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('FILIERE_STORE_VALIDATION_FAILED', [
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $filiere = Filiere::create($request->all());
            
            // Invalider le cache
            Cache::forget('filieres.all');
            
            ActionLogger::logAction('FILIERE_STORE_SUCCESS', [
                'filiere_code' => $filiere->code_filiere,
                'filiere_data' => $filiere->toArray()
            ]);
            
            return response()->json($filiere, 201);
        } catch (\Exception $e) {
            ActionLogger::logError('FILIERE_STORE_FAILED', $e, [
                'data' => $request->all()
            ]);
            return response()->json(['message' => 'Erreur lors de la création'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($code_filiere)
    {
        ActionLogger::logAction('FILIERE_SHOW', ['code_filiere' => $code_filiere]);
        
        $filiere = Cache::remember("filiere.{$code_filiere}", 3600, function () use ($code_filiere) {
            return Filiere::find($code_filiere);
        });

        if (!$filiere) {
            ActionLogger::logAction('FILIERE_SHOW_NOT_FOUND', ['code_filiere' => $code_filiere], 'warning');
            return response()->json(['message' => 'Filière non trouvée'], 404);
        }

        ActionLogger::logAction('FILIERE_SHOW_SUCCESS', [
            'code_filiere' => $code_filiere,
            'from_cache' => Cache::has("filiere.{$code_filiere}")
        ]);

        return response()->json($filiere, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $code_filiere)
    {
        ActionLogger::logAction('FILIERE_UPDATE_ATTEMPT', [
            'code_filiere' => $code_filiere,
            'data' => $request->all()
        ]);

        $filiere = Filiere::find($code_filiere);

        if (!$filiere) {
            ActionLogger::logAction('FILIERE_UPDATE_NOT_FOUND', ['code_filiere' => $code_filiere], 'warning');
            return response()->json(['message' => 'Filière non trouvée'], 404);
        }

        $validator = Validator::make($request->all(), [
            'label_filiere' => 'sometimes|required|string|max:256',
            'desc_filiere' => 'sometimes|required|string'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('FILIERE_UPDATE_VALIDATION_FAILED', [
                'code_filiere' => $code_filiere,
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $originalData = $filiere->toArray();

        try {
            $filiere->update($request->all());
            
            // Invalider le cache
            Cache::forget('filieres.all');
            Cache::forget("filiere.{$code_filiere}");
            
            ActionLogger::logAction('FILIERE_UPDATE_SUCCESS', [
                'code_filiere' => $code_filiere,
                'changes' => array_diff_assoc($filiere->toArray(), $originalData)
            ]);
            
            return response()->json($filiere, 200);
        } catch (\Exception $e) {
            ActionLogger::logError('FILIERE_UPDATE_FAILED', $e, [
                'code_filiere' => $code_filiere,
                'data' => $request->all()
            ]);
            return response()->json(['message' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($code_filiere)
    {
        ActionLogger::logAction('FILIERE_DELETE_ATTEMPT', ['code_filiere' => $code_filiere]);

        $filiere = Filiere::find($code_filiere);

        if (!$filiere) {
            ActionLogger::logAction('FILIERE_DELETE_NOT_FOUND', ['code_filiere' => $code_filiere], 'warning');
            return response()->json(['message' => 'Filière non trouvée'], 404);
        }

        $filiereData = $filiere->toArray();

        try {
            $filiere->delete();
            
            // Invalider le cache
            Cache::forget('filieres.all');
            Cache::forget("filiere.{$code_filiere}");
            
            ActionLogger::logAction('FILIERE_DELETE_SUCCESS', [
                'code_filiere' => $code_filiere,
                'deleted_data' => $filiereData
            ]);
            
            return response()->json(['message' => 'Filière supprimée avec succès'], 200);
        } catch (\Exception $e) {
            ActionLogger::logError('FILIERE_DELETE_FAILED', $e, [
                'code_filiere' => $code_filiere
            ]);
            return response()->json(['message' => 'Erreur lors de la suppression'], 500);
        }
    }
}
