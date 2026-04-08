# Monitoring — GestAcad

Stack de monitoring complète : **Prometheus** (collecte) + **Grafana** (visualisation) + **Node Exporter** (métriques système) + **Alertmanager** (alertes).

---

## Architecture

```
Laravel (PHP)
    │
    ├── GET /api/metrics  ──────────────────► Prometheus (scrape toutes les 10s)
    │       │                                       │
    │   PrometheusMetrics                           ├── node-exporter (CPU/RAM)
    │   (middleware)                                └── alertmanager (alertes)
    │
    └── MetricsController                   Grafana ◄── Prometheus
        (jauges DB live)                    :3000        :9090
```

---

## Démarrage

```bash
docker-compose up -d
```

| Service       | URL                          | Identifiants       |
|---------------|------------------------------|--------------------|
| Laravel       | http://localhost:8000        | —                  |
| Prometheus    | http://localhost:9090        | —                  |
| Grafana       | http://localhost:3000        | admin / admin      |
| Alertmanager  | http://localhost:9093        | —                  |
| Node Exporter | http://localhost:9100/metrics| —                  |

---

## Métriques exposées

Le endpoint `/api/metrics` (public, pas de token requis) expose les métriques au format Prometheus text.

### Métriques HTTP

| Métrique | Type | Description |
|---|---|---|
| `laravel_http_requests_total` | Counter | Nombre total de requêtes, labels : `method`, `route`, `status` |
| `laravel_http_request_duration_seconds` | Histogram | Durée des requêtes en secondes, labels : `method`, `route` |

### Métriques métier (mises à jour à chaque scrape)

| Métrique | Type | Description |
|---|---|---|
| `laravel_db_filieres_total` | Gauge | Nombre de filières en base |
| `laravel_db_personnels_total` | Gauge | Nombre de membres du personnel |
| `laravel_db_ecs_total` | Gauge | Nombre d'éléments constitutifs |
| `laravel_db_programmations_total` | Gauge | Nombre de séances programmées |

### Métriques d'authentification

| Métrique | Type | Description |
|---|---|---|
| `laravel_auth_attempts_total` | Counter | Tentatives de connexion, label `status` : `success` ou `failure` |

### Métriques système (node-exporter)

- CPU par mode (`idle`, `user`, `system`…)
- Mémoire disponible / totale
- Disque (lecture/écriture, espace libre)
- Réseau (bytes in/out)

---

## Implémentation PHP

### PrometheusServiceProvider

`app/Providers/PrometheusServiceProvider.php` — enregistre le `CollectorRegistry` comme singleton `app('prometheus')` avec tous les compteurs, histogrammes et jauges.

### PrometheusMetrics (middleware)

`app/Http/Middleware/PrometheusMetrics.php` — appliqué globalement via `bootstrap/app.php`. Intercepte chaque requête, mesure la durée et incrémente les compteurs HTTP.

### MetricsController

`app/Http/Controllers/MetricsController.php` — répond à `GET /api/metrics`. Met à jour les jauges DB (COUNT en base) puis rend le format texte Prometheus.

---

## Dashboard Grafana

Le dashboard **GestAcad — Laravel Monitoring** est provisionné automatiquement au démarrage depuis `monitoring/grafana/provisioning/dashboards/gestacad.json`.

Panneaux disponibles :

- Requêtes HTTP totales (stat)
- Taux d'erreurs 5xx (stat + seuil rouge)
- Latence p50 et p95 (stat)
- Requêtes par statut HTTP (time series)
- Durée des requêtes p50 / p95 / p99 (time series)
- Compteurs métier : filières, personnel, EC, programmations (stats)
- Tentatives d'authentification succès/échec (time series)
- CPU & RAM via node-exporter (time series)

La datasource Prometheus est également provisionnée automatiquement depuis `monitoring/grafana/provisioning/datasources/prometheus.yml`.

---

## Règles d'alerte

Fichier : `monitoring/alerts.yml`

| Alerte | Condition | Sévérité |
|---|---|---|
| `HighErrorRate` | > 0.5 req/s en 5xx pendant 2 min | critical |
| `SlowRequests` | p95 latence > 2s pendant 5 min | warning |
| `HighAuthFailures` | > 1 échec auth/s pendant 1 min | warning |
| `AppDown` | endpoint `/api/metrics` injoignable | critical |

---

## Requêtes PromQL utiles

```promql
# Taux de requêtes par seconde
sum(rate(laravel_http_requests_total[1m]))

# Taux d'erreurs 5xx
sum(rate(laravel_http_requests_total{status=~"5.."}[5m]))

# Latence p95
histogram_quantile(0.95,
  sum(rate(laravel_http_request_duration_seconds_bucket[5m])) by (le)
)

# Requêtes par route (top 10)
topk(10, sum by (route) (rate(laravel_http_requests_total[5m])))

# Taux d'échecs d'authentification
rate(laravel_auth_attempts_total{status="failure"}[5m])

# CPU utilisé (%)
100 - (avg by(instance)(rate(node_cpu_seconds_total{mode="idle"}[5m])) * 100)

# RAM utilisée (%)
(1 - (node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes)) * 100
```

---

## Structure des fichiers

```
monitoring/
├── prometheus.yml                          # Config Prometheus (scrape + alertes)
├── alerts.yml                              # Règles d'alerte
└── grafana/
    └── provisioning/
        ├── datasources/
        │   └── prometheus.yml              # Datasource auto-provisionnée
        └── dashboards/
            ├── dashboard.yml               # Config du provider
            └── gestacad.json               # Dashboard principal

app/
├── Providers/PrometheusServiceProvider.php # Enregistrement du registry
├── Http/Middleware/PrometheusMetrics.php   # Collecte automatique par requête
└── Http/Controllers/MetricsController.php # Endpoint /api/metrics
```
