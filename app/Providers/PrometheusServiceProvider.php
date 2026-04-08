<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;

class PrometheusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('prometheus', function () {
            $registry = new CollectorRegistry(new InMemory());

            // ── Compteurs HTTP ────────────────────────────────────────────
            $registry->registerCounter(
                'laravel',
                'http_requests_total',
                'Total HTTP requests',
                ['method', 'route', 'status']
            );

            // ── Histogramme durée des requêtes ────────────────────────────
            $registry->registerHistogram(
                'laravel',
                'http_request_duration_seconds',
                'HTTP request duration in seconds',
                ['method', 'route'],
                [0.01, 0.05, 0.1, 0.25, 0.5, 1.0, 2.5, 5.0]
            );

            // ── Métriques métier ──────────────────────────────────────────
            $registry->registerGauge(
                'laravel',
                'db_filieres_total',
                'Total number of filieres in DB'
            );
            $registry->registerGauge(
                'laravel',
                'db_personnels_total',
                'Total number of personnels in DB'
            );
            $registry->registerGauge(
                'laravel',
                'db_programmations_total',
                'Total number of programmations in DB'
            );
            $registry->registerGauge(
                'laravel',
                'db_ecs_total',
                'Total number of ECs in DB'
            );

            // ── Compteur d'authentifications ──────────────────────────────
            $registry->registerCounter(
                'laravel',
                'auth_attempts_total',
                'Total authentication attempts',
                ['status']  // success | failure
            );

            return $registry;
        });
    }

    public function boot(): void
    {
        //
    }
}
