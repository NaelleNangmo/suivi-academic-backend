<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ViewLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:view 
                            {--date= : Date spécifique (YYYY-MM-DD)}
                            {--action= : Filtrer par action}
                            {--user= : Filtrer par utilisateur}
                            {--tail=50 : Nombre de lignes à afficher}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consulter les logs de trace de l\'application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ?: now()->format('Y-m-d');
        $action = $this->option('action');
        $user = $this->option('user');
        $tail = $this->option('tail');

        $logFile = storage_path("logs/trace/app-trace-{$date}.log");

        if (!File::exists($logFile)) {
            $this->error("Aucun fichier de log trouvé pour la date {$date}");
            return 1;
        }

        $this->info("Lecture du fichier de log : {$logFile}");
        $this->line('');

        $content = File::get($logFile);
        $lines = explode("\n", $content);
        
        // Filtrer les lignes si nécessaire
        if ($action || $user) {
            $lines = array_filter($lines, function($line) use ($action, $user) {
                if ($action && !str_contains($line, $action)) {
                    return false;
                }
                if ($user && !str_contains($line, "user_id\":{$user}")) {
                    return false;
                }
                return true;
            });
        }

        // Prendre les dernières lignes
        $lines = array_slice($lines, -$tail);

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            // Essayer de décoder le JSON pour un affichage plus lisible
            if (str_contains($line, '{"timestamp"')) {
                $jsonStart = strpos($line, '{"timestamp"');
                $jsonPart = substr($line, $jsonStart);
                
                try {
                    $data = json_decode($jsonPart, true);
                    if ($data) {
                        $timestamp = $data['timestamp'] ?? 'N/A';
                        $action = $data['action'] ?? 'N/A';
                        $userId = $data['user_id'] ?? 'N/A';
                        
                        $this->line("<fg=cyan>[{$timestamp}]</> <fg=yellow>{$action}</> <fg=green>User:{$userId}</>");
                        
                        if (isset($data['data']) && !empty($data['data'])) {
                            $this->line("  Data: " . json_encode($data['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        }
                        $this->line('');
                        continue;
                    }
                } catch (\Exception $e) {
                    // Si le JSON n'est pas valide, afficher la ligne brute
                }
            }
            
            $this->line($line);
        }

        return 0;
    }
}