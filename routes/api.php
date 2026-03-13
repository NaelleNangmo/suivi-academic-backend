<?php

use App\Http\Controllers\FiliereController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\UeController;
use App\Http\Controllers\EcController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\EnseigneController;
use App\Http\Controllers\ProgrammationController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ----------------------------------------------------
// AUTH PUBLIC
// ----------------------------------------------------
Route::post('/login', [AuthController::class, 'login']);

// ----------------------------------------------------
// ROUTES PROTÉGÉES PAR SANCTUM
// ----------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // --- Utilisateur actuellement connecté ---
    Route::get('/me', function () {
        return response()->json(auth()->user(), 200);
    });

    // --- Logout ---
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- CRUD standards ---
    Route::apiResource('filieres', FiliereController::class);
    Route::apiResource('salles', SalleController::class);
    Route::apiResource('niveaux', NiveauController::class);
    Route::apiResource('ues', UeController::class);
    Route::apiResource('ecs', EcController::class);
    Route::apiResource('personnels', PersonnelController::class);

    // --- ROUTES POUR LE SUPPORT DE COURS ---
    Route::get('ecs/{code_ec}/support-cours', [EcController::class, 'downloadSupportCours']);
    Route::delete('ecs/{code_ec}/support-cours', [EcController::class, 'deleteSupportCours']);

    // --- ENSEIGNE (clé composite) ---
    Route::prefix('enseignes')->group(function () {
        Route::get('/', [EnseigneController::class, 'index']);
        Route::post('/', [EnseigneController::class, 'store']);
        Route::get('/{code_pers}/{code_ec}', [EnseigneController::class, 'show']);
        Route::delete('/{code_pers}/{code_ec}', [EnseigneController::class, 'destroy']);
    });

    // --- PROGRAMMATION (clé composite) ---
    Route::prefix('programmations')->group(function () {
        Route::get('/', [ProgrammationController::class, 'index']);
        Route::post('/', [ProgrammationController::class, 'store']);
        Route::get('/{code_ec}/{num_salle}/{code_pers}', [ProgrammationController::class, 'show']);
        Route::put('/{code_ec}/{num_salle}/{code_pers}', [ProgrammationController::class, 'update']);
        Route::delete('/{code_ec}/{num_salle}/{code_pers}', [ProgrammationController::class, 'destroy']);
    });

    // --- LOGS DE TRACE ---
    Route::prefix('logs')->group(function () {
        Route::get('/', [\App\Http\Controllers\LogController::class, 'index']);
        Route::get('/{date}', [\App\Http\Controllers\LogController::class, 'show']);
        Route::get('/{date}/stats', [\App\Http\Controllers\LogController::class, 'stats']);
    });
});