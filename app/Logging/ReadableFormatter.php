<?php

namespace App\Logging;

use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

class ReadableFormatter implements FormatterInterface
{
    /**
     * Formate un log de manière lisible et structurée
     */
    public function format(LogRecord $record): string
    {
        $timestamp = $record->datetime->format('Y-m-d H:i:s');
        $level = strtoupper($record->level->name);
        $message = $record->message;
        $context = $record->context ?? [];

        // Format de base avec timestamp et niveau
        $output = "\n" . str_repeat('=', 100) . "\n";
        $output .= sprintf("[%s] [%s] %s\n", $timestamp, $level, $message);
        $output .= str_repeat('-', 100) . "\n";

        // Extraction des informations principales
        $user = $this->extractUserInfo($context);
        $action = $this->extractAction($context, $message);
        $request = $this->extractRequestInfo($context);
        $response = $this->extractResponseInfo($context);
        $data = $this->extractData($context);

        // Affichage structuré
        if ($user) {
            $output .= "👤 UTILISATEUR:\n";
            foreach ($user as $key => $value) {
                if ($value !== null) {
                    $output .= sprintf("   %s: %s\n", $this->formatKey($key), $value);
                }
            }
            $output .= "\n";
        }

        if ($action) {
            $output .= "⚡ ACTION:\n";
            $output .= sprintf("   %s\n", $action);
            $output .= "\n";
        }

        if ($request) {
            $output .= "📥 REQUÊTE:\n";
            foreach ($request as $key => $value) {
                if ($value !== null && $value !== '') {
                    $formattedValue = is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $value;
                    $output .= sprintf("   %s: %s\n", $this->formatKey($key), $formattedValue);
                }
            }
            $output .= "\n";
        }

        if ($response) {
            $output .= "📤 RÉPONSE:\n";
            foreach ($response as $key => $value) {
                if ($value !== null) {
                    $output .= sprintf("   %s: %s\n", $this->formatKey($key), $value);
                }
            }
            $output .= "\n";
        }

        if ($data && !empty($data)) {
            $output .= "📋 DONNÉES:\n";
            foreach ($data as $key => $value) {
                if ($value !== null) {
                    $formattedValue = is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $value;
                    // Limiter la longueur pour la lisibilité
                    if (is_string($formattedValue) && strlen($formattedValue) > 200) {
                        $formattedValue = substr($formattedValue, 0, 200) . '...';
                    }
                    $output .= sprintf("   %s: %s\n", $this->formatKey($key), $formattedValue);
                }
            }
            $output .= "\n";
        }

        $output .= str_repeat('=', 100) . "\n";

        return $output;
    }

    public function formatBatch(array $records): string
    {
        $output = '';
        foreach ($records as $record) {
            $output .= $this->format($record);
        }
        return $output;
    }

    /**
     * Extrait les informations utilisateur
     */
    private function extractUserInfo(array $context): array
    {
        $user = [];

        // Récupération du username (login_pers) - priorité au user_username
        if (isset($context['user_username']) && $context['user_username'] !== null) {
            $user['Username'] = $context['user_username'];
        } elseif (isset($context['data']['login_pers'])) {
            $user['Username'] = $context['data']['login_pers'];
        } elseif (isset($context['login_pers'])) {
            $user['Username'] = $context['login_pers'];
        }

        // Récupération du user_id
        if (isset($context['user_id']) && $context['user_id'] !== null) {
            $user['ID'] = $context['user_id'];
        } elseif (isset($context['data']['personnel_code'])) {
            $user['ID'] = $context['data']['personnel_code'];
        } elseif (isset($context['personnel_code'])) {
            $user['ID'] = $context['personnel_code'];
        } elseif (isset($context['data']['user_code'])) {
            $user['ID'] = $context['data']['user_code'];
        } elseif (isset($context['user_code'])) {
            $user['ID'] = $context['user_code'];
        }

        // Nom complet
        if (isset($context['data']['personnel_nom'])) {
            $user['Nom'] = $context['data']['personnel_nom'];
        } elseif (isset($context['personnel_nom'])) {
            $user['Nom'] = $context['personnel_nom'];
        } elseif (isset($context['data']['user_nom'])) {
            $user['Nom'] = $context['data']['user_nom'];
        } elseif (isset($context['user_nom'])) {
            $user['Nom'] = $context['user_nom'];
        }

        // Prénom
        if (isset($context['data']['personnel_prenom'])) {
            $user['Prénom'] = $context['data']['personnel_prenom'];
        } elseif (isset($context['personnel_prenom'])) {
            $user['Prénom'] = $context['personnel_prenom'];
        } elseif (isset($context['data']['user_prenom'])) {
            $user['Prénom'] = $context['data']['user_prenom'];
        } elseif (isset($context['user_prenom'])) {
            $user['Prénom'] = $context['user_prenom'];
        }

        // Type
        if (isset($context['data']['personnel_type'])) {
            $user['Type'] = $context['data']['personnel_type'];
        } elseif (isset($context['personnel_type'])) {
            $user['Type'] = $context['personnel_type'];
        } elseif (isset($context['data']['user_type'])) {
            $user['Type'] = $context['data']['user_type'];
        } elseif (isset($context['user_type'])) {
            $user['Type'] = $context['user_type'];
        }

        // Email si disponible
        if (isset($context['user_email']) && $context['user_email'] !== null) {
            $user['Email'] = $context['user_email'];
        }

        // IP - toujours afficher si disponible
        if (isset($context['ip'])) {
            $user['Adresse IP'] = $context['ip'];
        }

        // User Agent - toujours afficher si disponible
        if (isset($context['user_agent'])) {
            $user['User Agent'] = $this->shortenUserAgent($context['user_agent']);
        }

        // Si aucun utilisateur identifié mais qu'on a une IP, on affiche au moins l'IP
        if (empty($user) && isset($context['ip'])) {
            $user['Adresse IP'] = $context['ip'];
            if (isset($context['user_agent'])) {
                $user['User Agent'] = $this->shortenUserAgent($context['user_agent']);
            }
        }

        return $user;
    }

    /**
     * Extrait l'action
     */
    private function extractAction(array $context, string $message): ?string
    {
        $action = null;
        $description = null;

        // Récupération de l'action
        if (isset($context['action'])) {
            $action = $context['action'];
        } elseif (in_array($message, ['ACTION_LOG', 'REQUEST_START', 'REQUEST_END'])) {
            $action = $message;
        } else {
            $action = $message;
        }

        // Récupération de la description si disponible
        if (isset($context['data']['action_description'])) {
            $description = $context['data']['action_description'];
        } elseif (isset($context['action_description'])) {
            $description = $context['action_description'];
        }

        // Formatage final
        if ($description) {
            return sprintf("%s - %s", $action, $description);
        }

        return $action;
    }

    /**
     * Extrait les informations de requête
     */
    private function extractRequestInfo(array $context): array
    {
        $request = [];

        if (isset($context['method'])) {
            $request['Méthode'] = $context['method'];
        }

        if (isset($context['url'])) {
            $request['URL'] = $context['url'];
        }

        if (isset($context['query_params']) && !empty($context['query_params'])) {
            $request['Paramètres'] = json_encode($context['query_params'], JSON_UNESCAPED_UNICODE);
        }

        return $request;
    }

    /**
     * Extrait les informations de réponse
     */
    private function extractResponseInfo(array $context): array
    {
        $response = [];

        if (isset($context['status_code'])) {
            $statusCode = $context['status_code'];
            $statusText = $this->getStatusText($statusCode);
            $response['Code Statut'] = sprintf('%d (%s)', $statusCode, $statusText);
        }

        if (isset($context['duration_ms'])) {
            $response['Durée'] = sprintf('%.2f ms', $context['duration_ms']);
        }

        if (isset($context['response_size'])) {
            $response['Taille'] = $this->formatBytes($context['response_size']);
        }

        return $response;
    }

    /**
     * Extrait les données supplémentaires
     */
    private function extractData(array $context): array
    {
        $data = [];

        // Exclure les clés déjà traitées dans les autres sections
        $excludedKeys = [
            'timestamp', 'action', 'user_id', 'user_email', 'user_username', 'ip', 'user_agent',
            'session_id', 'method', 'url', 'query_params', 'body', 'headers',
            'status_code', 'duration_ms', 'response_size', 'login_pers', 'personnel_code',
            'personnel_nom', 'personnel_prenom', 'personnel_type', 'user_code', 'user_nom',
            'user_prenom', 'user_type', 'action_description'
        ];

        foreach ($context as $key => $value) {
            if (!in_array($key, $excludedKeys) && $key !== 'data' && $value !== null) {
                // Ne pas inclure les tableaux vides
                if (is_array($value) && empty($value)) {
                    continue;
                }
                $data[$key] = $value;
            }
        }

        // Traiter le sous-tableau 'data'
        if (isset($context['data']) && is_array($context['data'])) {
            foreach ($context['data'] as $key => $value) {
                if (!in_array($key, $excludedKeys) && $value !== null) {
                    // Ne pas inclure les tableaux vides
                    if (is_array($value) && empty($value)) {
                        continue;
                    }
                    $data[$key] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * Formate une clé pour l'affichage
     */
    private function formatKey(string $key): string
    {
        return ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Raccourcit le user agent
     */
    private function shortenUserAgent(string $userAgent): string
    {
        if (strlen($userAgent) > 80) {
            return substr($userAgent, 0, 77) . '...';
        }
        return $userAgent;
    }

    /**
     * Retourne le texte du code de statut HTTP
     */
    private function getStatusText(int $code): string
    {
        $statusTexts = [
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
        ];

        return $statusTexts[$code] ?? 'Unknown';
    }

    /**
     * Formate les bytes en unités lisibles
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

