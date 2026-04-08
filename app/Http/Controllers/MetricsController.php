<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Personnel;
use App\Models\Programmation;
use App\Models\Ec;
use App\Models\Niveau;
use App\Models\Ue;
use App\Models\Salle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MetricsController extends Controller
{
    public function __invoke()
    {
        $lines = [];

        // ── Métriques métier DB ───────────────────────────────────────────
        $dbMetrics = [
            ['laravel_db_filieres_total',       'Total filieres in DB',       fn() => Filiere::count()],
            ['laravel_db_niveaux_total',         'Total niveaux in DB',        fn() => Niveau::count()],
            ['laravel_db_ues_total',             'Total UEs in DB',            fn() => Ue::count()],
            ['laravel_db_ecs_total',             'Total ECs in DB',            fn() => Ec::count()],
            ['laravel_db_personnels_total',      'Total personnels in DB',     fn() => Personnel::count()],
            ['laravel_db_salles_total',          'Total salles in DB',         fn() => Salle::count()],
            ['laravel_db_programmations_total',  'Total programmations in DB', fn() => Programmation::count()],
        ];

        foreach ($dbMetrics as [$name, $help, $query]) {
            try {
                $value = $query();
            } catch (\Throwable) {
                $value = 0;
            }
            $lines[] = "# HELP {$name} {$help}";
            $lines[] = "# TYPE {$name} gauge";
            $lines[] = "{$name} {$value}";
        }

        // ── Métriques HTTP depuis le cache (alimentées par le middleware) ─
        $httpTotal = Cache::get('prom_http_requests_total', []);
        if (!empty($httpTotal)) {
            $lines[] = '# HELP laravel_http_requests_total Total HTTP requests';
            $lines[] = '# TYPE laravel_http_requests_total counter';
            foreach ($httpTotal as $labels => $count) {
                $lines[] = "laravel_http_requests_total{{$labels}} {$count}";
            }
        }

        $httpDuration = Cache::get('prom_http_duration_sum', []);
        $httpDurationCount = Cache::get('prom_http_duration_count', []);
        if (!empty($httpDuration)) {
            $lines[] = '# HELP laravel_http_request_duration_seconds HTTP request duration';
            $lines[] = '# TYPE laravel_http_request_duration_seconds summary';
            foreach ($httpDuration as $labels => $sum) {
                $count = $httpDurationCount[$labels] ?? 0;
                $lines[] = "laravel_http_request_duration_seconds_sum{{$labels}} {$sum}";
                $lines[] = "laravel_http_request_duration_seconds_count{{$labels}} {$count}";
            }
        }

        // ── Métriques auth ────────────────────────────────────────────────
        $authMetrics = Cache::get('prom_auth_attempts_total', []);
        if (!empty($authMetrics)) {
            $lines[] = '# HELP laravel_auth_attempts_total Total auth attempts';
            $lines[] = '# TYPE laravel_auth_attempts_total counter';
            foreach ($authMetrics as $status => $count) {
                $lines[] = "laravel_auth_attempts_total{status=\"{$status}\"} {$count}";
            }
        }

        // ── Info PHP ──────────────────────────────────────────────────────
        $lines[] = '# HELP php_info PHP environment info';
        $lines[] = '# TYPE php_info gauge';
        $lines[] = 'php_info{version="' . PHP_VERSION . '"} 1';

        $output = implode("\n", $lines) . "\n";

        return response($output, 200)
            ->header('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');
    }
}
