<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PrometheusMetrics
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        /** @var Response $response */
        $response = $next($request);

        // Ne pas instrumenter le endpoint metrics lui-même
        if ($request->is('api/metrics')) {
            return $response;
        }

        try {
            $duration = microtime(true) - $start;
            $method   = $request->method();
            $status   = (string) $response->getStatusCode();
            $route    = $request->route()?->getName()
                     ?? $request->route()?->uri()
                     ?? 'unknown';

            // Clé de label pour le compteur
            $labelKey = "method=\"{$method}\",route=\"{$route}\",status=\"{$status}\"";

            // Incrémenter le compteur de requêtes (TTL 24h)
            $total = Cache::get('prom_http_requests_total', []);
            $total[$labelKey] = ($total[$labelKey] ?? 0) + 1;
            Cache::put('prom_http_requests_total', $total, now()->addDay());

            // Accumuler la durée
            $durationKey = "method=\"{$method}\",route=\"{$route}\"";
            $sums   = Cache::get('prom_http_duration_sum', []);
            $counts = Cache::get('prom_http_duration_count', []);
            $sums[$durationKey]   = ($sums[$durationKey]   ?? 0) + $duration;
            $counts[$durationKey] = ($counts[$durationKey] ?? 0) + 1;
            Cache::put('prom_http_duration_sum',   $sums,   now()->addDay());
            Cache::put('prom_http_duration_count', $counts, now()->addDay());

        } catch (\Throwable) {
            // Ne jamais faire planter l'app à cause du monitoring
        }

        return $response;
    }
}
