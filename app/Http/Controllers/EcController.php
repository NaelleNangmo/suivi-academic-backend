<?php

namespace App\Http\Controllers;

use App\Models\Ec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Services\ActionLogger;

class EcController extends BaseController
{
    public function index()
    {
        ActionLogger::logAction('EC_INDEX', ['action' => 'Liste des ECs consultée']);
        
        $ecs = Cache::remember('ecs.all', 3600, function () {
            return Ec::all();
        });
        
        ActionLogger::logAction('EC_INDEX_SUCCESS', [
            'count' => $ecs->count(),
            'from_cache' => Cache::has('ecs.all')
        ]);
        
        return response()->json($ecs, 200);
    }

    public function store(Request $request)
    {
        ActionLogger::logAction('EC_STORE_ATTEMPT', [
            'data' => $request->except(['support_cours'])
        ]);

        $validator = Validator::make($request->all(), [
            'code_ec' => 'required|string|max:20|unique:ec,code_ec',
            'label_ec' => 'required|string',
            'desc_ec' => 'required|string',
            'nbh_ec' => 'required|integer|min:1',
            'nbc_ec' => 'required|integer|min:1',
            'code_ue' => 'required|string|max:20|exists:ue,code_ue',
            'support_cours' => 'nullable|file|mimes:pdf|max:10240' // Max 10MB
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('EC_STORE_VALIDATION_FAILED', [
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('support_cours');

        // Gérer l'upload du fichier si présent
        if ($request->hasFile('support_cours')) {
            $file = $request->file('support_cours');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('cours', $fileName, 'public');
            $data['support_cours'] = $filePath;
            
            ActionLogger::logAction('EC_FILE_UPLOADED', [
                'original_name' => $file->getClientOriginalName(),
                'stored_path' => $filePath,
                'file_size' => $file->getSize()
            ]);
        }

        try {
            $ec = Ec::create($data);
            
            // Invalider le cache
            Cache::forget('ecs.all');
            
            ActionLogger::logAction('EC_STORE_SUCCESS', [
                'ec_code' => $ec->code_ec,
                'ec_data' => $ec->toArray()
            ]);
            
            return response()->json($ec, 201);
        } catch (\Exception $e) {
            ActionLogger::logError('EC_STORE_FAILED', $e, [
                'data' => $data
            ]);
            return response()->json(['message' => 'Erreur lors de la création'], 500);
        }
    }

    public function show($code_ec)
    {
        ActionLogger::logAction('EC_SHOW', ['code_ec' => $code_ec]);
        
        $ec = Cache::remember("ec.{$code_ec}", 3600, function () use ($code_ec) {
            return Ec::find($code_ec);
        });

        if (!$ec) {
            ActionLogger::logAction('EC_SHOW_NOT_FOUND', ['code_ec' => $code_ec], 'warning');
            return response()->json(['message' => 'EC non trouvé'], 404);
        }

        ActionLogger::logAction('EC_SHOW_SUCCESS', [
            'code_ec' => $code_ec,
            'from_cache' => Cache::has("ec.{$code_ec}")
        ]);

        return response()->json($ec, 200);
    }

    public function update(Request $request, $code_ec)
    {
        ActionLogger::logAction('EC_UPDATE_ATTEMPT', [
            'code_ec' => $code_ec,
            'data' => $request->except(['support_cours'])
        ]);

        $ec = Ec::find($code_ec);

        if (!$ec) {
            ActionLogger::logAction('EC_UPDATE_NOT_FOUND', ['code_ec' => $code_ec], 'warning');
            return response()->json(['message' => 'EC non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'label_ec' => 'sometimes|required|string',
            'desc_ec' => 'sometimes|required|string',
            'nbh_ec' => 'sometimes|required|integer|min:1',
            'nbc_ec' => 'sometimes|required|integer|min:1',
            'code_ue' => 'sometimes|required|string|max:20|exists:ue,code_ue',
            'support_cours' => 'nullable|file|mimes:pdf|max:10240'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('EC_UPDATE_VALIDATION_FAILED', [
                'code_ec' => $code_ec,
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $originalData = $ec->toArray();
        $data = $request->except('support_cours');

        // Gérer l'upload du nouveau fichier
        if ($request->hasFile('support_cours')) {
            // Supprimer l'ancien fichier s'il existe
            if ($ec->support_cours && Storage::disk('public')->exists($ec->support_cours)) {
                Storage::disk('public')->delete($ec->support_cours);
                ActionLogger::logAction('EC_OLD_FILE_DELETED', [
                    'code_ec' => $code_ec,
                    'deleted_file' => $ec->support_cours
                ]);
            }

            $file = $request->file('support_cours');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('cours', $fileName, 'public');
            $data['support_cours'] = $filePath;
            
            ActionLogger::logAction('EC_NEW_FILE_UPLOADED', [
                'code_ec' => $code_ec,
                'original_name' => $file->getClientOriginalName(),
                'stored_path' => $filePath,
                'file_size' => $file->getSize()
            ]);
        }

        try {
            $ec->update($data);
            
            // Invalider le cache
            Cache::forget('ecs.all');
            Cache::forget("ec.{$code_ec}");
            
            ActionLogger::logAction('EC_UPDATE_SUCCESS', [
                'code_ec' => $code_ec,
                'changes' => array_diff_assoc($ec->toArray(), $originalData)
            ]);
            
            return response()->json($ec, 200);
        } catch (\Exception $e) {
            ActionLogger::logError('EC_UPDATE_FAILED', $e, [
                'code_ec' => $code_ec,
                'data' => $data
            ]);
            return response()->json(['message' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    public function destroy($code_ec)
    {
        ActionLogger::logAction('EC_DELETE_ATTEMPT', ['code_ec' => $code_ec]);

        $ec = Ec::find($code_ec);

        if (!$ec) {
            ActionLogger::logAction('EC_DELETE_NOT_FOUND', ['code_ec' => $code_ec], 'warning');
            return response()->json(['message' => 'EC non trouvé'], 404);
        }

        $ecData = $ec->toArray();

        try {
            // Supprimer le fichier s'il existe
            if ($ec->support_cours && Storage::disk('public')->exists($ec->support_cours)) {
                Storage::disk('public')->delete($ec->support_cours);
                ActionLogger::logAction('EC_FILE_DELETED', [
                    'code_ec' => $code_ec,
                    'deleted_file' => $ec->support_cours
                ]);
            }

            $ec->delete();
            
            // Invalider le cache
            Cache::forget('ecs.all');
            Cache::forget("ec.{$code_ec}");
            
            ActionLogger::logAction('EC_DELETE_SUCCESS', [
                'code_ec' => $code_ec,
                'deleted_data' => $ecData
            ]);
            
            return response()->json(['message' => 'EC supprimé avec succès'], 200);
        } catch (\Exception $e) {
            ActionLogger::logError('EC_DELETE_FAILED', $e, [
                'code_ec' => $code_ec
            ]);
            return response()->json(['message' => 'Erreur lors de la suppression'], 500);
        }
    }

    /**
     * Télécharger le support de cours
     */
    public function downloadSupportCours($code_ec)
    {
        ActionLogger::logAction('EC_DOWNLOAD_ATTEMPT', ['code_ec' => $code_ec]);

        $ec = Ec::find($code_ec);

        if (!$ec) {
            ActionLogger::logAction('EC_DOWNLOAD_NOT_FOUND', ['code_ec' => $code_ec], 'warning');
            return response()->json(['message' => 'EC non trouvé'], 404);
        }

        if (!$ec->support_cours) {
            ActionLogger::logAction('EC_DOWNLOAD_NO_FILE', ['code_ec' => $code_ec], 'warning');
            return response()->json(['message' => 'Aucun support de cours disponible'], 404);
        }

        $filePath = storage_path('app/public/' . $ec->support_cours);

        if (!file_exists($filePath)) {
            ActionLogger::logAction('EC_DOWNLOAD_FILE_NOT_EXISTS', [
                'code_ec' => $code_ec,
                'file_path' => $filePath
            ], 'error');
            return response()->json(['message' => 'Fichier introuvable'], 404);
        }

        ActionLogger::logAction('EC_DOWNLOAD_SUCCESS', [
            'code_ec' => $code_ec,
            'file_path' => $ec->support_cours,
            'file_size' => filesize($filePath)
        ]);

        return response()->download($filePath);
    }

    /**
     * Supprimer le support de cours
     */
    public function deleteSupportCours($code_ec)
    {
        ActionLogger::logAction('EC_DELETE_SUPPORT_ATTEMPT', ['code_ec' => $code_ec]);

        $ec = Ec::find($code_ec);

        if (!$ec) {
            ActionLogger::logAction('EC_DELETE_SUPPORT_NOT_FOUND', ['code_ec' => $code_ec], 'warning');
            return response()->json(['message' => 'EC non trouvé'], 404);
        }

        if (!$ec->support_cours) {
            ActionLogger::logAction('EC_DELETE_SUPPORT_NO_FILE', ['code_ec' => $code_ec], 'warning');
            return response()->json(['message' => 'Aucun support de cours à supprimer'], 404);
        }

        $oldFile = $ec->support_cours;

        try {
            // Supprimer le fichier
            if (Storage::disk('public')->exists($ec->support_cours)) {
                Storage::disk('public')->delete($ec->support_cours);
            }

            // Mettre à jour l'EC
            $ec->update(['support_cours' => null]);

            // Invalider le cache
            Cache::forget('ecs.all');
            Cache::forget("ec.{$code_ec}");

            ActionLogger::logAction('EC_DELETE_SUPPORT_SUCCESS', [
                'code_ec' => $code_ec,
                'deleted_file' => $oldFile
            ]);

            return response()->json(['message' => 'Support de cours supprimé avec succès'], 200);
        } catch (\Exception $e) {
            ActionLogger::logError('EC_DELETE_SUPPORT_FAILED', $e, [
                'code_ec' => $code_ec,
                'file' => $oldFile
            ]);
            return response()->json(['message' => 'Erreur lors de la suppression'], 500);
        }
    }
}
{
    public function index()
    {
        $ecs = Cache::remember('ecs.all', 3600, function () {
            return Ec::all();
        });
        return response()->json($ecs, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code_ec' => 'required|string|max:20|unique:ec,code_ec',
            'label_ec' => 'required|string',
            'desc_ec' => 'required|string',
            'nbh_ec' => 'required|integer|min:1',
            'nbc_ec' => 'required|integer|min:1',
            'code_ue' => 'required|string|max:20|exists:ue,code_ue',
            'support_cours' => 'nullable|file|mimes:pdf|max:10240' // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('support_cours');

        // Gérer l'upload du fichier si présent
        if ($request->hasFile('support_cours')) {
            $file = $request->file('support_cours');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('cours', $fileName, 'public');
            $data['support_cours'] = $filePath;
        }

        $ec = Ec::create($data);
        
        // Invalider le cache
        Cache::forget('ecs.all');
        
        return response()->json($ec, 201);
    }

    public function show($code_ec)
    {
        $ec = Cache::remember("ec.{$code_ec}", 3600, function () use ($code_ec) {
            return Ec::find($code_ec);
        });

        if (!$ec) {
            return response()->json(['message' => 'EC non trouvé'], 404);
        }

        return response()->json($ec, 200);
    }

    public function update(Request $request, $code_ec)
    {
        $ec = Ec::find($code_ec);

        if (!$ec) {
            return response()->json(['message' => 'EC non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'label_ec' => 'sometimes|required|string',
            'desc_ec' => 'sometimes|required|string',
            'nbh_ec' => 'sometimes|required|integer|min:1',
            'nbc_ec' => 'sometimes|required|integer|min:1',
            'code_ue' => 'sometimes|required|string|max:20|exists:ue,code_ue',
            'support_cours' => 'nullable|file|mimes:pdf|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('support_cours');

        // Gérer l'upload du nouveau fichier
        if ($request->hasFile('support_cours')) {
            // Supprimer l'ancien fichier s'il existe
            if ($ec->support_cours && Storage::disk('public')->exists($ec->support_cours)) {
                Storage::disk('public')->delete($ec->support_cours);
            }

            $file = $request->file('support_cours');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('cours', $fileName, 'public');
            $data['support_cours'] = $filePath;
        }

        $ec->update($data);
        
        // Invalider le cache
        Cache::forget('ecs.all');
        Cache::forget("ec.{$code_ec}");
        
        return response()->json($ec, 200);
    }

    public function destroy($code_ec)
    {
        $ec = Ec::find($code_ec);

        if (!$ec) {
            return response()->json(['message' => 'EC non trouvé'], 404);
        }

        // Supprimer le fichier s'il existe
        if ($ec->support_cours && Storage::disk('public')->exists($ec->support_cours)) {
            Storage::disk('public')->delete($ec->support_cours);
        }

        $ec->delete();
        
        // Invalider le cache
        Cache::forget('ecs.all');
        Cache::forget("ec.{$code_ec}");
        
        return response()->json(['message' => 'EC supprimé avec succès'], 200);
    }

    /**
     * Télécharger le support de cours
     */
    public function downloadSupportCours($code_ec)
    {
        $ec = Ec::find($code_ec);

        if (!$ec) {
            return response()->json(['message' => 'EC non trouvé'], 404);
        }

        if (!$ec->support_cours) {
            return response()->json(['message' => 'Aucun support de cours disponible'], 404);
        }

        $filePath = storage_path('app/public/' . $ec->support_cours);

        if (!file_exists($filePath)) {
            return response()->json(['message' => 'Fichier introuvable'], 404);
        }

        return response()->download($filePath);
    }

    /**
     * Supprimer le support de cours
     */
    public function deleteSupportCours($code_ec)
    {
        $ec = Ec::find($code_ec);

        if (!$ec) {
            return response()->json(['message' => 'EC non trouvé'], 404);
        }

        if (!$ec->support_cours) {
            return response()->json(['message' => 'Aucun support de cours à supprimer'], 404);
        }

        // Supprimer le fichier
        if (Storage::disk('public')->exists($ec->support_cours)) {
            Storage::disk('public')->delete($ec->support_cours);
        }

        // Mettre à jour l'EC
        $ec->update(['support_cours' => null]);

        // Invalider le cache
        Cache::forget('ecs.all');
        Cache::forget("ec.{$code_ec}");

        return response()->json(['message' => 'Support de cours supprimé avec succès'], 200);
    }
}