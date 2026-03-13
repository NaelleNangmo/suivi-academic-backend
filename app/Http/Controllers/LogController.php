<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class LogController extends Controller
{
    /**
     * Lister les fichiers de logs disponibles
     */
    public function index()
    {
        $logPath = storage_path('logs/trace');
        
        if (!File::exists($logPath)) {
            return response()->json(['message' => 'Dossier de logs non trouvé'], 404);
        }

        $files = File::files($logPath);
        $logFiles = [];

        foreach ($files as $file) {
            $fileName = $file->getFilename();
            if (str_contains($fileName, 'app-trace-')) {
                $date = str_replace(['app-trace-', '.log'], '', $fileName);
                $logFiles[] = [
                    'date' => $date,
                    'file' => $fileName,
                    'size' => $file->getSize(),
                    'modified' => Carbon::createFromTimestamp($file->getMTime())->toISOString(),
                ];
            }
        }

        // Trier par date décroissante
        usort($logFiles, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return response()->json($logFiles);
    }

    /**
     * Consulter les logs d'une date spécifique
     */
    public function show(Request $request, $date)
    {
        $logFile = storage_path("logs/trace/app-trace-{$date}.log");

        if (!File::exists($logFile)) {
            return response()->json(['message' => 'Fichier de log non trouvé'], 404);
        }

        $limit = $request->get('limit', 100);
        $action = $request->get('action');
        $userId = $request->get('user_id');
        $level = $request->get('level');

        $content = File::get($logFile);
        $lines = explode("\n", $content);
        
        $logs = [];
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            // Extraire le JSON de la ligne de log
            if (str_contains($line, '{"timestamp"')) {
                $jsonStart = strpos($line, '{"timestamp"');
                $jsonPart = substr($line, $jsonStart);
                
                try {
                    $data = json_decode($jsonPart, true);
                    if ($data) {
                        // Appliquer les filtres
                        if ($action && !str_contains($data['action'] ?? '', $action)) {
                            continue;
                        }
                        if ($userId && ($data['user_id'] ?? null) != $userId) {
                            continue;
                        }
                        if ($level && !str_contains($line, strtoupper($level))) {
                            continue;
                        }
                        
                        $logs[] = $data;
                    }
                } catch (\Exception $e) {
                    // Ignorer les lignes avec JSON invalide
                }
            }
        }

        // Prendre les dernières entrées
        $logs = array_slice($logs, -$limit);

        return response()->json([
            'date' => $date,
            'total_entries' => count($logs),
            'logs' => $logs
        ]);
    }

    /**
     * Statistiques des logs
     */
    public function stats($date)
    {
        $logFile = storage_path("logs/trace/app-trace-{$date}.log");

        if (!File::exists($logFile)) {
            return response()->json(['message' => 'Fichier de log non trouvé'], 404);
        }

        $content = File::get($logFile);
        $lines = explode("\n", $content);
        
        $stats = [
            'total_entries' => 0,
            'actions' => [],
            'users' => [],
            'levels' => [],
            'hourly_distribution' => [],
        ];

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            if (str_contains($line, '{"timestamp"')) {
                $jsonStart = strpos($line, '{"timestamp"');
                $jsonPart = substr($line, $jsonStart);
                
                try {
                    $data = json_decode($jsonPart, true);
                    if ($data) {
                        $stats['total_entries']++;
                        
                        // Compter les actions
                        $action = $data['action'] ?? 'UNKNOWN';
                        $stats['actions'][$action] = ($stats['actions'][$action] ?? 0) + 1;
                        
                        // Compter les utilisateurs
                        $userId = $data['user_id'] ?? 'anonymous';
                        $stats['users'][$userId] = ($stats['users'][$userId] ?? 0) + 1;
                        
                        // Extraire le niveau du log
                        if (str_contains($line, '.ERROR:')) {
                            $level = 'ERROR';
                        } elseif (str_contains($line, '.WARNING:')) {
                            $level = 'WARNING';
                        } else {
                            $level = 'INFO';
                        }
                        $stats['levels'][$level] = ($stats['levels'][$level] ?? 0) + 1;
                        
                        // Distribution horaire
                        if (isset($data['timestamp'])) {
                            $hour = Carbon::parse($data['timestamp'])->format('H');
                            $stats['hourly_distribution'][$hour] = ($stats['hourly_distribution'][$hour] ?? 0) + 1;
                        }
                    }
                } catch (\Exception $e) {
                    // Ignorer les lignes avec JSON invalide
                }
            }
        }

        // Trier les statistiques
        arsort($stats['actions']);
        arsort($stats['users']);
        ksort($stats['hourly_distribution']);

        return response()->json($stats);
    }
}