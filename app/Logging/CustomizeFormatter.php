<?php

namespace App\Logging;

use Monolog\Logger;

class CustomizeFormatter
{
    /**
     * Personnalise le logger pour utiliser notre formatter lisible
     */
    public function __invoke(Logger $logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new ReadableFormatter());
        }
    }
}


