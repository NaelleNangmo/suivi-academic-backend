<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogAllRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Récupération des informations utilisateur
        $user = auth()->user();
        $username = null;
        if ($user) {
            $username = $user->login_pers ?? $user->email ?? $user->name ?? null;
        }
        
        // Log de la requête entrante
        Log::channel('daily_trace')->info('REQUEST_START', [
            'timestamp' => now()->toISOString(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'query_params' => $request->query(),
            'body' => $request->except(['password', 'password_confirmation', 'pwd_pers']), // Exclure les mots de passe
            'user_id' => auth()->id(),
            'user_username' => $username,
            'user_email' => $user?->email ?? null,
            'session_id' => session()->getId(),
        ]);

        $response = $next($request);
        
        $endTime = microtime(true);
        $duration = ($endTime - $startTime) * 1000; // en millisecondes

        // Log de la réponse avec informations utilisateur
        Log::channel('daily_trace')->info('REQUEST_END', [
            'timestamp' => now()->toISOString(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => round($duration, 2),
            'response_size' => strlen($response->getContent()),
            'user_id' => auth()->id(),
            'user_username' => $username,
            'user_email' => $user?->email ?? null,
            'ip' => $request->ip(),
        ]);

        return $response;
    }
}