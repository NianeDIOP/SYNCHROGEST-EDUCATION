<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MatiereController;

// Route d'accueil - Sélection du module
Route::get('/', function () {
    return Inertia::render('Welcome');
});

// Routes d'authentification
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Module Inscription
Route::prefix('inscriptions')->middleware('auth')->group(function () {
    Route::get('/', [InscriptionController::class, 'dashboard'])->name('inscriptions.dashboard');
    Route::get('/parametres', [ParametreController::class, 'inscription'])->name('inscriptions.parametres');
    Route::post('/parametres', [ParametreController::class, 'saveInscription']);
    Route::get('/import', [InscriptionController::class, 'showImport'])->name('inscriptions.import');
    Route::post('/import', [InscriptionController::class, 'processImport']);
    Route::get('/eleves', [InscriptionController::class, 'eleves'])->name('inscriptions.eleves');
    Route::get('/nouvelle', [InscriptionController::class, 'nouvelleInscription'])->name('inscriptions.nouvelle');
    Route::post('/nouvelle', [InscriptionController::class, 'enregistrerInscription']);
    Route::get('/rapports', [InscriptionController::class, 'rapports'])->name('inscriptions.rapports');
});

// Module Finance
Route::prefix('finances')->middleware('auth')->group(function () {
    Route::get('/', [FinanceController::class, 'dashboard'])->name('finances.dashboard');
    Route::get('/parametres', [ParametreController::class, 'finance'])->name('finances.parametres');
    // Autres routes du module finance...
});

// Module Matière
Route::prefix('matieres')->middleware('auth')->group(function () {
    Route::get('/', [MatiereController::class, 'dashboard'])->name('matieres.dashboard');
    Route::get('/parametres', [ParametreController::class, 'matiere'])->name('matieres.parametres');
    // Autres routes du module matière...
});