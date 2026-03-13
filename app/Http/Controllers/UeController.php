<?php

namespace App\Http\Controllers;

use App\Models\Ue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Services\ActionLogger;

class UeController extends Controller
{
    public function index()
    {
        ActionLogger::logAction('UE_INDEX', ['action' => 'Liste des UEs consultée']);
        
        $ues = Cache::remember('ues.all', 3600, function () {
            return Ue::all();
        });
        
        ActionLogger::logAction('UE_INDEX_SUCCESS', [
            'count' => $ues->count(),
            'from_cache' => Cache::has('ues.all')
        ]);
        
        return response()->json($ues, 200);
    }

    public function store(Request $request)
    {
        ActionLogger::logAction('UE_STORE_ATTEMPT', [
            'data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'code_ue' => 'required|string|max:20|unique:ue,code_ue',
            'label_ue' => 'required|string',
            'desc_ue' => 'required|string',
            'code_niveau' => 'required|integer|exists:niveau,code_niveau'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('UE_STORE_VALIDATION_FAILED', [
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $ue = Ue::create($request->all());
            
            // Invalider le cache
            Cache::forget('ues.all');
            
            ActionLogger::logAction('UE_STORE_SUCCESS', [
                'ue_code' => $ue->code_ue,
                'ue_data' => $ue->toArray()
            ]);
            
            return response()->json($ue, 201);
        } catch (\Exception $e) {
            ActionLogger::logError('UE_STORE_FAILED', $e, [
                'data' => $request->all()
            ]);
            return response()->json(['message' => 'Erreur lors de la création'], 500);
        }
    }

    public function show($code_ue)
    {
        ActionLogger::logAction('UE_SHOW', ['code_ue' => $code_ue]);
        
        $ue = Cache::remember("ue.{$code_ue}", 3600, function () use ($code_ue) {
            return Ue::find($code_ue);
        });

        if (!$ue) {
            ActionLogger::logAction('UE_SHOW_NOT_FOUND', ['code_ue' => $code_ue], 'warning');
            return response()->json(['message' => 'UE non trouvée'], 404);
        }

        ActionLogger::logAction('UE_SHOW_SUCCESS', [
            'code_ue' => $code_ue,
            'from_cache' => Cache::has("ue.{$code_ue}")
        ]);

        return response()->json($ue, 200);
    }

    public function update(Request $request, $code_ue)
    {
        ActionLogger::logAction('UE_UPDATE_ATTEMPT', [
            'code_ue' => $code_ue,
            'data' => $request->all()
        ]);

        $ue = Ue::find($code_ue);

        if (!$ue) {
            ActionLogger::logAction('UE_UPDATE_NOT_FOUND', ['code_ue' => $code_ue], 'warning');
            return response()->json(['message' => 'UE non trouvée'], 404);
        }

        $validator = Validator::make($request->all(), [
            'label_ue' => 'sometimes|required|string',
            'desc_ue' => 'sometimes|required|string',
            'code_niveau' => 'sometimes|required|integer|exists:niveau,code_niveau'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('UE_UPDATE_VALIDATION_FAILED', [
                'code_ue' => $code_ue,
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $originalData = $ue->toArray();

        try {
            $ue->update($request->all());
            
            // Invalider le cache
            Cache::forget('ues.all');
            Cache::forget("ue.{$code_ue}");
            
            ActionLogger::logAction('UE_UPDATE_SUCCESS', [
                'code_ue' => $code_ue,
                'changes' => array_diff_assoc($ue->toArray(), $originalData)
            ]);
            
            return response()->json($ue, 200);
        } catch (\Exception $e) {
            ActionLogger::logError('UE_UPDATE_FAILED', $e, [
                'code_ue' => $code_ue,
                'data' => $request->all()
            ]);
            return response()->json(['message' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    public function destroy($code_ue)
    {
        ActionLogger::logAction('UE_DELETE_ATTEMPT', ['code_ue' => $code_ue]);

        $ue = Ue::find($code_ue);

        if (!$ue) {
            ActionLogger::logAction('UE_DELETE_NOT_FOUND', ['code_ue' => $code_ue], 'warning');
            return response()->json(['message' => 'UE non trouvée'], 404);
        }

        $ueData = $ue->toArray();

        try {
            $ue->delete();
            
            // Invalider le cache
            Cache::forget('ues.all');
            Cache::forget("ue.{$code_ue}");
            
            ActionLogger::logAction('UE_DELETE_SUCCESS', [
                'code_ue' => $code_ue,
                'deleted_data' => $ueData
            ]);
            
            return response()->json(['message' => 'UE supprimée avec succès'], 200);
        } catch (\Exception $e) {
            ActionLogger::logError('UE_DELETE_FAILED', $e, [
                'code_ue' => $code_ue
            ]);
            return response()->json(['message' => 'Erreur lors de la suppression'], 500);
        }
    }
}
