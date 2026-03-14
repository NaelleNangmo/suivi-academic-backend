<?php

namespace App\Logging;

use Monolog\Logger;
use Illuminate\Log\Logger as IlluminateLogger;

class CustomizeFormatter
{
    /**
     * Personnalise le logger pour utiliser notre formatter lisible
     */
    public function __invoke(IlluminateLogger|Logger $logger)
    {
        $monolog = $logger instanceof IlluminateLogger ? $logger->getLogger() : $logger;
        foreach ($monolog->getHandlers() as $handler) {
            $handler->setFormatter(new ReadableFormatter());
        }
    }
}


