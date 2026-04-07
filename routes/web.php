<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WebViewController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/filieres',       [WebViewController::class, 'filieres']);
Route::get('/niveaux',        [WebViewController::class, 'niveaux']);
Route::get('/ues',            [WebViewController::class, 'ues']);
Route::get('/ecs',            [WebViewController::class, 'ecs']);
Route::get('/personnels',     [WebViewController::class, 'personnels']);
Route::get('/salles',         [WebViewController::class, 'salles']);
Route::get('/programmations', [WebViewController::class, 'programmations']);
Route::get('/enseignes',      [WebViewController::class, 'enseignes']);
Route::get('/docs',           [WebViewController::class, 'docs']);
