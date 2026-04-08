<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Services\ActionLogger;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        ActionLogger::logAction('AUTH_LOGIN_ATTEMPT', [
            'login_pers' => $request->login_pers,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'action_description' => 'Tentative de connexion'
        ]);

        $request->validate([
            'login_pers' => 'required|string',
            'pwd_pers'   => 'required|string'
        ]);

        // Recherche du personnel
        $personnel = Personnel::where('login_pers', $request->login_pers)->first();

        if (!$personnel) {
            ActionLogger::logAction('AUTH_LOGIN_USER_NOT_FOUND', [
                'login_pers' => $request->login_pers,
                'ip' => $ip,
                'user_agent' => $userAgent,
                'action_description' => 'Échec de connexion - Utilisateur non trouvé'
            ], 'warning');
            $this->prometheusAuthCount('failure');
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        if (!Hash::check($request->pwd_pers, $personnel->pwd_pers)) {
            ActionLogger::logAction('AUTH_LOGIN_WRONG_PASSWORD', [
                'login_pers' => $request->login_pers,
                'personnel_code' => $personnel->code_pers,
                'personnel_nom' => $personnel->nom_pers,
                'ip' => $ip,
                'user_agent' => $userAgent,
                'action_description' => 'Échec de connexion - Mot de passe incorrect'
            ], 'warning');
            $this->prometheusAuthCount('failure');
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        try {
            // Suppression des anciens tokens
            $deletedTokens = DB::table('personal_access_tokens')
                ->where('tokenable_id', $personnel->code_pers)
                ->where('tokenable_type', Personnel::class)
                ->count();
            
            DB::table('personal_access_tokens')
                ->where('tokenable_id', $personnel->code_pers)
                ->where('tokenable_type', Personnel::class)
                ->delete();

            if ($deletedTokens > 0) {
                ActionLogger::logAction('AUTH_OLD_TOKENS_DELETED', [
                    'personnel_code' => $personnel->code_pers,
                    'personnel_nom' => $personnel->nom_pers,
                    'login_pers' => $personnel->login_pers,
                    'deleted_tokens_count' => $deletedTokens,
                    'ip' => $ip,
                    'action_description' => 'Anciens tokens supprimés avant nouvelle connexion'
                ]);
            }

            // Token : expire dans 1 heure
            $expiration = Carbon::now()->addHours(1);

            $token = $personnel->createToken('auth_token', [], $expiration)->plainTextToken;

            ActionLogger::logAction('AUTH_LOGIN_SUCCESS', [
                'personnel_code' => $personnel->code_pers,
                'personnel_nom' => $personnel->nom_pers,
                'personnel_prenom' => $personnel->prenom_pers ?? null,
                'personnel_type' => $personnel->type_pers,
                'login_pers' => $personnel->login_pers,
                'token_expires_at' => $expiration->toISOString(),
                'ip' => $ip,
                'user_agent' => $userAgent,
                'action_description' => 'Connexion réussie'
            ]);

            $this->prometheusAuthCount('success');
            return response()->json([
                'personnel'    => $personnel,
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'expires_at'   => $expiration
            ], 200);
        } catch (\Exception $e) {
            ActionLogger::logError('AUTH_LOGIN_TOKEN_CREATION_FAILED', $e, [
                'personnel_code' => $personnel->code_pers ?? null,
                'login_pers' => $request->login_pers,
                'ip' => $ip,
                'action_description' => 'Erreur lors de la création du token'
            ]);
            return response()->json(['message' => 'Erreur lors de la création du token'], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        ActionLogger::logAction('AUTH_LOGOUT_ATTEMPT', [
            'user_code' => $user?->code_pers,
            'user_username' => $user?->login_pers ?? null,
            'user_nom' => $user?->nom_pers ?? null,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'action_description' => 'Tentative de déconnexion'
        ]);

        if ($user && $user->currentAccessToken()) {
            try {
                $tokenId = $user->currentAccessToken()->id;
                $user->currentAccessToken()->delete();

                ActionLogger::logAction('AUTH_LOGOUT_SUCCESS', [
                    'user_code' => $user->code_pers,
                    'user_username' => $user->login_pers ?? null,
                    'user_nom' => $user->nom_pers,
                    'user_prenom' => $user->prenom_pers ?? null,
                    'user_type' => $user->type_pers ?? null,
                    'token_id' => $tokenId,
                    'ip' => $ip,
                    'user_agent' => $userAgent,
                    'action_description' => 'Déconnexion réussie'
                ]);

                return response()->json(['message' => 'Déconnexion réussie'], 200);
            } catch (\Exception $e) {
                ActionLogger::logError('AUTH_LOGOUT_FAILED', $e, [
                    'user_code' => $user->code_pers,
                    'user_username' => $user->login_pers ?? null,
                    'ip' => $ip,
                    'action_description' => 'Erreur lors de la déconnexion'
                ]);
                return response()->json(['message' => 'Erreur lors de la déconnexion'], 500);
            }
        }

        ActionLogger::logAction('AUTH_LOGOUT_NO_TOKEN', [
            'ip' => $ip,
            'user_agent' => $userAgent,
            'action_description' => 'Tentative de déconnexion sans token valide'
        ], 'warning');

        return response()->json(['message' => 'Aucun token à supprimer'], 200);
    }

    private function prometheusAuthCount(string $status): void
    {
        try {
            $counts = \Illuminate\Support\Facades\Cache::get('prom_auth_attempts_total', []);
            $counts[$status] = ($counts[$status] ?? 0) + 1;
            \Illuminate\Support\Facades\Cache::put('prom_auth_attempts_total', $counts, now()->addDay());
        } catch (\Throwable) {
            // silencieux
        }
    }
}
