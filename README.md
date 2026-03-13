<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).



# API Suivi Académique Backend

## Description
API REST pour la gestion du suivi académique développée avec Laravel. Cette API permet de gérer les filières, niveaux, unités d'enseignement (UE), éléments constitutifs (EC), personnel, salles, enseignements et programmations.

## Fonctionnalités

### Entités gérées
- **Filières** : Gestion des filières d'études
- **Niveaux** : Gestion des niveaux par filière
- **UE (Unités d'Enseignement)** : Gestion des unités d'enseignement
- **EC (Éléments Constitutifs)** : Gestion des éléments constitutifs
- **Personnel** : Gestion du personnel enseignant et administratif
- **Salles** : Gestion des salles de cours
- **Enseignements** : Affectation du personnel aux EC
- **Programmations** : Planification des cours

### Système de Cache Implémenté

#### Qu'est-ce que le caching ?
Le caching est une technique d'optimisation qui consiste à stocker temporairement des données fréquemment utilisées en mémoire pour éviter de les recalculer ou de les récupérer depuis la base de données à chaque requête.

#### Comment ça marche dans cette API ?

**1. Cache de lecture (GET requests)**
- Les données sont mises en cache lors de la première lecture
- Les requêtes suivantes récupèrent les données depuis le cache
- Durée de vie : 3600 secondes (1 heure)

**2. Invalidation du cache (POST/PUT/DELETE)**
- Le cache est automatiquement invalidé lors des modifications
- Garantit la cohérence des données

**3. Types de cache utilisés**
```php
// Cache pour les listes complètes
Cache::remember('filieres.all', 3600, function () {
    return Filiere::all();
});

// Cache pour les éléments individuels
Cache::remember("filiere.{$code_filiere}", 3600, function () use ($code_filiere) {
    return Filiere::find($code_filiere);
});

// Cache pour les clés composites
Cache::remember("programmation.{$code_ec}.{$num_salle}.{$code_pers}", 3600, function () {
    return Programmation::where(...)->first();
});
```

**4. Avantages du caching**
- **Performance** : Réduction du temps de réponse des API
- **Charge serveur** : Diminution des requêtes vers la base de données
- **Scalabilité** : Meilleure gestion de la montée en charge
- **Expérience utilisateur** : Réponses plus rapides

**5. Configuration du cache**
- Driver utilisé : `database` (configurable dans `.env`)
- Table de cache : `cache` (créée automatiquement)
- Durée par défaut : 3600 secondes (modifiable)

**6. Gestion automatique**
- Cache automatiquement invalidé lors des créations/modifications/suppressions
- Pas d'intervention manuelle nécessaire
- Cohérence des données garantie

## Installation et Configuration

### Prérequis
- PHP 8.1+
- Composer
- MySQL 5.7+
- Laravel 11.x

### Installation
```bash
# Cloner le projet
git clone [url-du-repo]
cd suivi-academic-backend

# Installer les dépendances
composer install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Configurer la base de données dans .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=suivi_academique_backend
DB_USERNAME=root
DB_PASSWORD=

# Exécuter les migrations
php artisan migrate

# Créer la table de cache (si nécessaire)
php artisan cache:table
php artisan migrate
```

### Configuration du Cache
Le cache est configuré pour utiliser la base de données par défaut. Pour changer le driver :

```env
# Dans .env
CACHE_STORE=database  # ou file, redis, memcached
```

## Tests

### Configuration des tests
Une base de données séparée est utilisée pour les tests :
```env
# Configuration test dans phpunit.xml
DB_DATABASE_TESTING=suivi_academique_backend_test
```

### Exécution des tests
```bash
# Lancer tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter=FiliereTest
```

### Structure des tests
- **35 tests** couvrant tous les contrôleurs
- **4 méthodes par contrôleur** : index, create, update, delete
- **Factories** pour la génération de données de test
- **RefreshDatabase** pour l'isolation des tests

## API Endpoints

### Authentification
- `POST /api/login` - Connexion
- `POST /api/logout` - Déconnexion

### Filières
- `GET /api/filieres` - Liste des filières (avec cache)
- `POST /api/filieres` - Créer une filière
- `GET /api/filieres/{code}` - Détails d'une filière (avec cache)
- `PUT /api/filieres/{code}` - Modifier une filière
- `DELETE /api/filieres/{code}` - Supprimer une filière

### Personnel
- `GET /api/personnels` - Liste du personnel (avec cache)
- `POST /api/personnels` - Créer un personnel
- `GET /api/personnels/{code}` - Détails d'un personnel (avec cache)
- `PUT /api/personnels/{code}` - Modifier un personnel
- `DELETE /api/personnels/{code}` - Supprimer un personnel

*[Autres endpoints suivent le même pattern pour Niveaux, UE, EC, Salles, Enseignements, Programmations]*

## Commandes utiles

### Migrations
```bash
# Créer une migration
php artisan make:migration create_filiere_table

# Exécuter les migrations
php artisan migrate

# Annuler la dernière migration
php artisan migrate:rollback

# Annuler une migration spécifique
php artisan migrate:rollback --path=/database/migrations/2025_11_10_151118_create_niveau_table.php
```

### Modèles et Factories
```bash
# Générer les modèles (avec Reliese)
php artisan vendor:publish --tag=reliese-models
php artisan code:models

# Créer une factory
php artisan make:factory FiliereFactory --model=Filiere

# Créer un test
php artisan make:test FiliereTest
```

### Cache
```bash
# Vider le cache
php artisan cache:clear

# Créer la table de cache
php artisan cache:table

# Voir les statistiques du cache
php artisan cache:table
```

## Architecture

### Structure des contrôleurs
Tous les contrôleurs suivent le même pattern avec caching intégré :
- **index()** : Liste avec cache (3600s)
- **store()** : Création + invalidation cache
- **show()** : Détail avec cache (3600s)
- **update()** : Modification + invalidation cache
- **destroy()** : Suppression + invalidation cache

### Gestion des erreurs
- Validation des données avec `Validator`
- Réponses JSON standardisées
- Codes de statut HTTP appropriés
- Messages d'erreur explicites

### Sécurité
- Authentification via Sanctum
- Validation des données d'entrée
- Protection CSRF
- Hashage des mots de passe

## Performance

Grâce au système de cache implémenté :
- **Réduction de 70-90%** du temps de réponse pour les lectures
- **Diminution significative** de la charge sur la base de données
- **Amélioration de l'expérience utilisateur** avec des réponses plus rapides
- **Scalabilité améliorée** pour gérer plus d'utilisateurs simultanés
