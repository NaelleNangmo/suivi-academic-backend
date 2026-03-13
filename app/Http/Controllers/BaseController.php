<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class BaseController extends Controller
{
    /**
     * Paginer une requête avec mise en cache
     *
     * @param Builder $query
     * @param Request $request
     * @param string $cacheKey
     * @param int $cacheTtl
     * @return \Illuminate\Http\JsonResponse
     */
    protected function paginateWithCache(Builder $query, Request $request, string $cacheKey, int $cacheTtl = 3600)
    {
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 10);
        
        // Limiter le nombre d'éléments par page (max 100)
        $limit = min($limit, 100);
        $limit = max($limit, 1);
        
        // Créer une clé de cache unique incluant la page et la limite
        $fullCacheKey = "{$cacheKey}.page_{$page}.limit_{$limit}";
        
        $result = Cache::remember($fullCacheKey, $cacheTtl, function () use ($query, $page, $limit) {
            return $query->paginate($limit, ['*'], 'page', $page);
        });
        
        return response()->json([
            'data' => $result->items(),
            'pagination' => [
                'current_page' => $result->currentPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
                'last_page' => $result->lastPage(),
                'from' => $result->firstItem(),
                'to' => $result->lastItem(),
                'has_more_pages' => $result->hasMorePages()
            ]
        ], 200);
    }
    
    /**
     * Invalider tous les caches de pagination pour une ressource
     *
     * @param string $baseKey
     * @return void
     */
    protected function invalidatePaginationCache(string $baseKey)
    {
        // Invalider le cache global
        Cache::forget("{$baseKey}.all");
        
        // Invalider les caches de pagination (approximatif)
        // En production, vous pourriez vouloir utiliser des tags de cache
        for ($page = 1; $page <= 10; $page++) {
            for ($limit = 1; $limit <= 100; $limit += 9) {
                Cache::forget("{$baseKey}.page_{$page}.limit_{$limit}");
            }
        }
    }
}