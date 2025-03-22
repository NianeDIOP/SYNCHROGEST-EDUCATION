<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\AuthController;

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Module Inscription
Route::prefix('inscriptions')->middleware('auth')->group(function () {
    Route::get('/', [InscriptionController::class, 'dashboard'])->name('inscriptions.dashboard');
    
    // Paramètres
    Route::get('/parametres', [InscriptionController::class, 'parametres'])->name('inscriptions.parametres');
    Route::post('/parametres', [InscriptionController::class, 'saveParametres']);
    
    // Niveaux et classes
    Route::get('/niveaux', [InscriptionController::class, 'niveaux'])->name('inscriptions.niveaux');
    Route::post('/niveaux', [InscriptionController::class, 'saveNiveaux']);
    
    // Importation
    Route::get('/import', [InscriptionController::class, 'showImport'])->name('inscriptions.import');
    Route::post('/import', [InscriptionController::class, 'processImport']);
    Route::post('/import/save', [InscriptionController::class, 'saveImportedData'])->name('inscriptions.saveImportedData');
    
    // Élèves
    Route::get('/eleves', [InscriptionController::class, 'eleves'])->name('inscriptions.eleves');
    Route::get('/eleves/{id}', [InscriptionController::class, 'showEleve']);
    Route::delete('/eleves/{id}', [InscriptionController::class, 'deleteEleve']);
    // Élèves
    Route::get('/inscriptions/eleves', [InscriptionController::class, 'eleves'])->name('inscriptions.eleves');
    Route::get('/inscriptions/eleves/{id}', [InscriptionController::class, 'showEleve']);
    Route::delete('/inscriptions/eleves/{id}', [InscriptionController::class, 'deleteEleve']);
        
    // Inscription
    Route::get('/nouvelle', [InscriptionController::class, 'nouvelleInscription'])->name('inscriptions.nouvelle');
    Route::post('/nouvelle', [InscriptionController::class, 'enregistrerInscription']);
    Route::get('/recu/{id}', [InscriptionController::class, 'recu'])->name('inscriptions.recu');
    
    // Rapports
    Route::get('/rapports', [InscriptionController::class, 'rapports'])->name('inscriptions.rapports');
    Route::post('/rapports/generer', [InscriptionController::class, 'genererRapport'])->name('inscriptions.genererRapport');
});

// Module Finance (juste les routes de base pour l'instant)
Route::prefix('finances')->middleware('auth')->group(function () {
    Route::get('/', [FinanceController::class, 'dashboard'])->name('finances.dashboard');
    Route::get('/parametres', [FinanceController::class, 'parametres'])->name('finances.parametres');
});

// Module Matière (juste les routes de base pour l'instant)
Route::prefix('matieres')->middleware('auth')->group(function () {
    Route::get('/', [MatiereController::class, 'dashboard'])->name('matieres.dashboard');
    Route::get('/parametres', [MatiereController::class, 'parametres'])->name('matieres.parametres');
});

