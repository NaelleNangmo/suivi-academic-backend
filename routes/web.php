<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WebViewController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/filieres',       [WebViewController::class, 'filieres']);
Route::post('/filieres',      [WebViewController::class, 'filieresStore']);
Route::put('/filieres/{id}',  [WebViewController::class, 'filieresUpdate']);
Route::delete('/filieres/{id}', [WebViewController::class, 'filieresDestroy']);

Route::get('/niveaux',        [WebViewController::class, 'niveaux']);
Route::post('/niveaux',       [WebViewController::class, 'niveauxStore']);
Route::put('/niveaux/{id}',   [WebViewController::class, 'niveauxUpdate']);
Route::delete('/niveaux/{id}',[WebViewController::class, 'niveauxDestroy']);

Route::get('/ues',            [WebViewController::class, 'ues']);
Route::post('/ues',           [WebViewController::class, 'uesStore']);
Route::put('/ues/{id}',       [WebViewController::class, 'uesUpdate']);
Route::delete('/ues/{id}',    [WebViewController::class, 'uesDestroy']);

Route::get('/ecs',            [WebViewController::class, 'ecs']);
Route::post('/ecs',           [WebViewController::class, 'ecsStore']);
Route::put('/ecs/{id}',       [WebViewController::class, 'ecsUpdate']);
Route::delete('/ecs/{id}',    [WebViewController::class, 'ecsDestroy']);

Route::get('/personnels',     [WebViewController::class, 'personnels']);
Route::post('/personnels',    [WebViewController::class, 'personnelsStore']);
Route::put('/personnels/{id}',[WebViewController::class, 'personnelsUpdate']);
Route::delete('/personnels/{id}', [WebViewController::class, 'personnelsDestroy']);

Route::get('/salles',         [WebViewController::class, 'salles']);
Route::post('/salles',        [WebViewController::class, 'sallesStore']);
Route::put('/salles/{id}',    [WebViewController::class, 'sallesUpdate']);
Route::delete('/salles/{id}', [WebViewController::class, 'sallesDestroy']);

Route::get('/programmations', [WebViewController::class, 'programmations']);
Route::post('/programmations',[WebViewController::class, 'programmationsStore']);
Route::put('/programmations/{ec}/{salle}/{pers}',    [WebViewController::class, 'programmationsUpdate']);
Route::delete('/programmations/{ec}/{salle}/{pers}', [WebViewController::class, 'programmationsDestroy']);

Route::get('/enseignes',      [WebViewController::class, 'enseignes']);
Route::post('/enseignes',     [WebViewController::class, 'enseignesStore']);
Route::delete('/enseignes/{pers}/{ec}', [WebViewController::class, 'enseignesDestroy']);

Route::get('/docs',           [WebViewController::class, 'docs']);
