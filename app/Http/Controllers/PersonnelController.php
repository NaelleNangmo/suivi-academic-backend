<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Services\ActionLogger;
use App\Mail\PersonnelWelcomeMail;

class PersonnelController extends Controller
{
    public function index()
    {
        ActionLogger::logAction('PERSONNEL_INDEX', ['action' => 'Liste du personnel consultée']);
        
        $personnels = Cache::remember('personnels.all', 3600, function () {
            return Personnel::all();
        });
        
        ActionLogger::logAction('PERSONNEL_INDEX_SUCCESS', [
            'count' => $personnels->count(),
            'from_cache' => Cache::has('personnels.all')
        ]);
        
        return response()->json($personnels, 200);
    }

    public function store(Request $request)
    {
        ActionLogger::logAction('PERSONNEL_STORE_ATTEMPT', [
            'data' => $request->except(['pwd_pers']) // Exclure le mot de passe des logs
        ]);

        $validator = Validator::make($request->all(), [
            'code_pers' => 'required|string|unique:personnel,code_pers',
            'nom_pers' => 'required|string',
            'prenom_pers' => 'nullable|string',
            'sexe_pers' => 'required|in:M,F',
            'phone_pers' => 'required|string',
            'login_pers' => 'required|email|unique:personnel,login_pers',
            'pwd_pers' => 'required|string|min:6',
            'type_pers' => 'required|in:ENSEIGNANT,RESPONSABLE ACADEMIQUE,RESPONSABLE DISCIPLINE'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('PERSONNEL_STORE_VALIDATION_FAILED', [
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->all();
            $plainPassword = $request->pwd_pers; // Sauvegarder le mot de passe en clair avant hash
            $data['pwd_pers'] = Hash::make($plainPassword);

            $personnel = Personnel::create($data);
            
            // Invalider le cache
            Cache::forget('personnels.all');
            
            // Envoyer l'email de bienvenue à l'adresse login_pers
            try {
                Mail::to($personnel->login_pers)->send(new PersonnelWelcomeMail($personnel, $plainPassword));
                
                ActionLogger::logAction('PERSONNEL_WELCOME_EMAIL_SENT', [
                    'personnel_code' => $personnel->code_pers,
                    'email' => $personnel->login_pers
                ]);
            } catch (\Exception $emailException) {
                // Logger l'erreur d'email mais ne pas faire échouer la création
                ActionLogger::logError('PERSONNEL_WELCOME_EMAIL_FAILED', $emailException, [
                    'personnel_code' => $personnel->code_pers,
                    'email' => $personnel->login_pers
                ]);
            }
            
            ActionLogger::logAction('PERSONNEL_STORE_SUCCESS', [
                'personnel_code' => $personnel->code_pers,
                'personnel_data' => $personnel->makeHidden(['pwd_pers'])->toArray() // Masquer le mot de passe
            ]);
            
            return response()->json($personnel, 201);
        } catch (\Exception $e) {
            ActionLogger::logError('PERSONNEL_STORE_FAILED', $e, [
                'data' => $request->except(['pwd_pers'])
            ]);
            return response()->json(['message' => 'Erreur lors de la création'], 500);
        }
    }

    public function show($code_pers)
    {
        ActionLogger::logAction('PERSONNEL_SHOW', ['code_pers' => $code_pers]);
        
        $personnel = Cache::remember("personnel.{$code_pers}", 3600, function () use ($code_pers) {
            return Personnel::find($code_pers);
        });

        if (!$personnel) {
            ActionLogger::logAction('PERSONNEL_SHOW_NOT_FOUND', ['code_pers' => $code_pers], 'warning');
            return response()->json(['message' => 'Personnel non trouvé'], 404);
        }

        ActionLogger::logAction('PERSONNEL_SHOW_SUCCESS', [
            'code_pers' => $code_pers,
            'from_cache' => Cache::has("personnel.{$code_pers}")
        ]);

        return response()->json($personnel, 200);
    }

    public function update(Request $request, $code_pers)
    {
        ActionLogger::logAction('PERSONNEL_UPDATE_ATTEMPT', [
            'code_pers' => $code_pers,
            'data' => $request->except(['pwd_pers']) // Exclure le mot de passe des logs
        ]);

        $personnel = Personnel::find($code_pers);

        if (!$personnel) {
            ActionLogger::logAction('PERSONNEL_UPDATE_NOT_FOUND', ['code_pers' => $code_pers], 'warning');
            return response()->json(['message' => 'Personnel non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nom_pers' => 'sometimes|required|string',
            'prenom_pers' => 'nullable|string',
            'sexe_pers' => 'sometimes|required|in:M,F',
            'phone_pers' => 'sometimes|required|string',
            'login_pers' => 'sometimes|required|email|unique:personnel,login_pers,'.$code_pers.',code_pers',
            'pwd_pers' => 'sometimes|required|string|min:6',
            'type_pers' => 'sometimes|required|in:ENSEIGNANT,RESPONSABLE ACADEMIQUE,RESPONSABLE DISCIPLINE'
        ]);

        if ($validator->fails()) {
            ActionLogger::logAction('PERSONNEL_UPDATE_VALIDATION_FAILED', [
                'code_pers' => $code_pers,
                'errors' => $validator->errors()->toArray()
            ], 'warning');
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $originalData = $personnel->makeHidden(['pwd_pers'])->toArray();

        try {
            $data = $request->all();
            if ($request->has('pwd_pers')) {
                $data['pwd_pers'] = Hash::make($request->pwd_pers);
                ActionLogger::logAction('PERSONNEL_PASSWORD_CHANGED', [
                    'code_pers' => $code_pers
                ]);
            }

            $personnel->update($data);
            
            // Invalider le cache
            Cache::forget('personnels.all');
            Cache::forget("personnel.{$code_pers}");
            
            $newData = $personnel->makeHidden(['pwd_pers'])->toArray();
            ActionLogger::logAction('PERSONNEL_UPDATE_SUCCESS', [
                'code_pers' => $code_pers,
                'changes' => array_diff_assoc($newData, $originalData)
            ]);
            
            return response()->json($personnel, 200);
        } catch (\Exception $e) {
            ActionLogger::logError('PERSONNEL_UPDATE_FAILED', $e, [
                'code_pers' => $code_pers,
                'data' => $request->except(['pwd_pers'])
            ]);
            return response()->json(['message' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    public function destroy($code_pers)
    {
        ActionLogger::logAction('PERSONNEL_DELETE_ATTEMPT', ['code_pers' => $code_pers]);

        $personnel = Personnel::find($code_pers);

        if (!$personnel) {
            ActionLogger::logAction('PERSONNEL_DELETE_NOT_FOUND', ['code_pers' => $code_pers], 'warning');
            return response()->json(['message' => 'Personnel non trouvé'], 404);
        }

        $personnelData = $personnel->makeHidden(['pwd_pers'])->toArray();

        try {
            $personnel->delete();
            
            // Invalider le cache
            Cache::forget('personnels.all');
            Cache::forget("personnel.{$code_pers}");
            
            ActionLogger::logAction('PERSONNEL_DELETE_SUCCESS', [
                'code_pers' => $code_pers,
                'deleted_data' => $personnelData
            ]);
            
            return response()->json(['message' => 'Personnel supprimé avec succès'], 200);
        } catch (\Exception $e) {
            ActionLogger::logError('PERSONNEL_DELETE_FAILED', $e, [
                'code_pers' => $code_pers
            ]);
            return response()->json(['message' => 'Erreur lors de la suppression'], 500);
        }
    }
}
