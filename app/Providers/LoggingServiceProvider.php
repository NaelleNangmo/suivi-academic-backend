<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use App\Services\ActionLogger;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Log toutes les requêtes SQL si activé
        if (config('logging.log_queries', false)) {
            DB::listen(function ($query) {
                ActionLogger::logQuery(
                    $query->sql,
                    $query->bindings,
                    $query->time
                );
            });
        }

        // Log les événements d'authentification
        Event::listen(Login::class, function ($event) {
            ActionLogger::logAuth('LOGIN', $event->user);
        });

        Event::listen(Logout::class, function ($event) {
            ActionLogger::logAuth('LOGOUT', $event->user);
        });

        Event::listen(Failed::class, function ($event) {
            ActionLogger::logAuth('LOGIN_FAILED', null, [
                'credentials' => array_keys($event->credentials),
            ]);
        });
    }
}