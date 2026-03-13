<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ActionLogger
{
    /**
     * Log une action utilisateur
     */
    public static function logAction(string $action, array $data = [], string $level = 'info')
    {
        $user = Auth::user();
        
        // Récupération du username (login_pers pour Personnel)
        $username = null;
        if ($user) {
            $username = $user->login_pers ?? $user->email ?? $user->name ?? null;
        }

        $logData = [
            'timestamp' => now()->toISOString(),
            'action' => $action,
            'user_id' => Auth::id(),
            'user_username' => $username,
            'user_email' => $user?->email ?? null,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'data' => $data,
        ];

        Log::channel('daily_trace')->{$level}('ACTION_LOG', $logData);
    }

    /**
     * Log une création d'entité
     */
    public static function logCreate(string $model, $entity, array $additionalData = [])
    {
        self::logAction('CREATE', [
            'model' => $model,
            'entity_id' => $entity->id ?? null,
            'entity_data' => $entity->toArray(),
            ...$additionalData
        ]);
    }

    /**
     * Log une mise à jour d'entité
     */
    public static function logUpdate(string $model, $entity, array $changes = [], array $additionalData = [])
    {
        self::logAction('UPDATE', [
            'model' => $model,
            'entity_id' => $entity->id ?? null,
            'changes' => $changes,
            'new_data' => $entity->toArray(),
            ...$additionalData
        ]);
    }

    /**
     * Log une suppression d'entité
     */
    public static function logDelete(string $model, $entity, array $additionalData = [])
    {
        self::logAction('DELETE', [
            'model' => $model,
            'entity_id' => $entity->id ?? null,
            'deleted_data' => $entity->toArray(),
            ...$additionalData
        ]);
    }

    /**
     * Log une authentification
     */
    public static function logAuth(string $type, $user = null, array $additionalData = [])
    {
        $username = null;
        if ($user) {
            $username = $user->login_pers ?? $user->email ?? $user->name ?? null;
        }

        self::logAction('AUTH', [
            'auth_type' => $type,
            'user_id' => $user?->code_pers ?? $user?->id ?? null,
            'user_username' => $username,
            'user_email' => $user?->email ?? null,
            ...$additionalData
        ]);
    }

    /**
     * Log une erreur
     */
    public static function logError(string $message, \Throwable $exception = null, array $additionalData = [])
    {
        self::logAction('ERROR', [
            'message' => $message,
            'exception' => $exception ? [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ] : null,
            ...$additionalData
        ], 'error');
    }

    /**
     * Log une requête de base de données
     */
    public static function logQuery(string $sql, array $bindings = [], float $time = null)
    {
        self::logAction('DATABASE_QUERY', [
            'sql' => $sql,
            'bindings' => $bindings,
            'execution_time_ms' => $time,
        ]);
    }
}