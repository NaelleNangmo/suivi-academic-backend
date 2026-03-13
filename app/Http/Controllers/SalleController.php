<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Services\ActionLogger;

class SalleController extends Controller
{
    public function index()
    {
        ActionLogger::logAction('SALLE_INDEX', ['action' => 'Liste des salles consultée']);
        
        $salles = Cache::remember('salles.all', 3600, function () {
            return Salle::all();
        });
        
        ActionLogger::logAction('SALLE_INDEX_SUCCESS', [
            'count' => $salles->count(),
            'from_cache' => Cache::has('salles.all')
        ]);
        
        return response()->json($salles, 200);
    }

    public function store(Request $request)
    {
        ActionLogger::logAction('SALLE_STORE_ATTEMPT', [
            'data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'num_salle' => 'required|string|unique:salle,num_salle',
            'contenance' => 'required|integer|min:1',
            'statut' => 'required|in:DISPONIBLE,NON DISPONIBLE'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('SALLE_STORE_VALIDATION_FAILED', [
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $salle = Salle::create($request->all());
            
            // Invalider le cache
            Cache::forget('salles.all');
            
            ActionLogger::logAction('SALLE_STORE_SUCCESS', [
                'salle_num' => $salle->num_salle,
                'salle_data' => $salle->toArray()
            ]);
            
            return response()->json($salle, 201);
        } catch (\Exception $e) {
            ActionLogger::logError('SALLE_STORE_FAILED', $e, [
                'data' => $request->all()
            ]);
            return response()->json(['message' => 'Erreur lors de la création'], 500);
        }
    }

    public function show($num_salle)
    {
        ActionLogger::logAction('SALLE_SHOW', ['num_salle' => $num_salle]);
        
        $salle = Cache::remember("salle.{$num_salle}", 3600, function () use ($num_salle) {
            return Salle::find($num_salle);
        });

        if (!$salle) {
            ActionLogger::logAction('SALLE_SHOW_NOT_FOUND', ['num_salle' => $num_salle], 'warning');
            return response()->json(['message' => 'Salle non trouvée'], 404);
        }

        ActionLogger::logAction('SALLE_SHOW_SUCCESS', [
            'num_salle' => $num_salle,
            'from_cache' => Cache::has("salle.{$num_salle}")
        ]);

        return response()->json($salle, 200);
    }

    public function update(Request $request, $num_salle)
    {
        ActionLogger::logAction('SALLE_UPDATE_ATTEMPT', [
            'num_salle' => $num_salle,
            'data' => $request->all()
        ]);

        $salle = Salle::find($num_salle);

        if (!$salle) {
            ActionLogger::logAction('SALLE_UPDATE_NOT_FOUND', ['num_salle' => $num_salle], 'warning');
            return response()->json(['message' => 'Salle non trouvée'], 404);
        }

        $validator = Validator::make($request->all(), [
            'contenance' => 'sometimes|required|integer|min:1',
            'statut' => 'sometimes|required|in:DISPONIBLE,NON DISPONIBLE'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('SALLE_UPDATE_VALIDATION_FAILED', [
                'num_salle' => $num_salle,
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $originalData = $salle->toArray();

        try {
            $salle->update($request->all());
            
            // Invalider le cache
            Cache::forget('salles.all');
            Cache::forget("salle.{$num_salle}");
            
            ActionLogger::logAction('SALLE_UPDATE_SUCCESS', [
                'num_salle' => $num_salle,
                'changes' => array_diff_assoc($salle->toArray(), $originalData)
            ]);
            
            return response()->json($salle, 200);
        } catch (\Exception $e) {
            ActionLogger::logError('SALLE_UPDATE_FAILED', $e, [
                'num_salle' => $num_salle,
                'data' => $request->all()
            ]);
            return response()->json(['message' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    public function destroy($num_salle)
    {
        ActionLogger::logAction('SALLE_DELETE_ATTEMPT', ['num_salle' => $num_salle]);

        $salle = Salle::find($num_salle);

        if (!$salle) {
            ActionLogger::logAction('SALLE_DELETE_NOT_FOUND', ['num_salle' => $num_salle], 'warning');
            return response()->json(['message' => 'Salle non trouvée'], 404);
        }

        $salleData = $salle->toArray();

        try {
            $salle->delete();
            
            // Invalider le cache
            Cache::forget('salles.all');
            Cache::forget("salle.{$num_salle}");
            
            ActionLogger::logAction('SALLE_DELETE_SUCCESS', [
                'num_salle' => $num_salle,
                'deleted_data' => $salleData
            ]);
            
            return response()->json(['message' => 'Salle supprimée avec succès'], 200);
        } catch (\Exception $e) {
            ActionLogger::logError('SALLE_DELETE_FAILED', $e, [
                'num_salle' => $num_salle
            ]);
            return response()->json(['message' => 'Erreur lors de la suppression'], 500);
        }
    }
}
